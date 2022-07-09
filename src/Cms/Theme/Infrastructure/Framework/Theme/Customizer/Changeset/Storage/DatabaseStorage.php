<?php

declare(strict_types=1);

namespace Tulia\Cms\Theme\Infrastructure\Framework\Theme\Customizer\Changeset\Storage;

use Doctrine\DBAL\Connection;
use Tulia\Component\Theme\Customizer\Changeset\ChangesetInterface;
use Tulia\Component\Theme\Customizer\Changeset\Factory\ChangesetFactoryInterface;
use Tulia\Component\Theme\Customizer\Changeset\Storage\StorageInterface;
use Tulia\Component\Theme\Enum\ChangesetTypeEnum;
use Tulia\Component\Theme\Exception\ChangesetNotFoundException;

/**
 * @author Adam Banaszkiewicz
 */
class DatabaseStorage implements StorageInterface
{
    public function __construct(
        protected Connection $connection,
        protected ChangesetFactoryInterface $changesetFactory,
    ) {
    }

    /**
     * {@inheritdoc}
     */
    public function has(string $id, string $locale): bool
    {
        return $this->getRow($id, $locale) !== [];
    }

    /**
     * {@inheritdoc}
     */
    public function getActiveChangeset(string $theme, string $locale): ?ChangesetInterface
    {
        $result = $this->connection->fetchAllAssociative('SELECT *
            FROM #__customizer_changeset AS tm
            INNER JOIN #__customizer_changeset_lang AS tl
                ON (tm.id = tl.customizer_changeset_id)
            WHERE tm.theme = :theme AND tm.type = :type AND tl.locale = :locale
            LIMIT 1', [
            'theme'  => $theme,
            'type'   => ChangesetTypeEnum::ACTIVE,
            'locale' => $locale,
        ]);

        if ($result === []) {
            return null;
        }

        return $this->buildChangesetFromDatabaseRow($result[0]);
    }

    /**
     * {@inheritdoc}
     */
    public function getTemporaryCopyOfActiveChangeset(string $theme): ChangesetInterface
    {
        $active = $this->getActiveChangeset($theme);

        if (!$active) {
            return $this->changesetFactory->factory();
        }

        $newId = $this->changesetFactory->factory()->getId();

        $this->makeCopyOf($active->getId(), $newId);

        $this->connection->update('#__customizer_changeset', [
            'created_at' => date('Y-m-d H:i:s'),
            'type'       => ChangesetTypeEnum::TEMPORARY,
        ], [
            'id' => $newId,
        ]);

        return $this->get($newId);
    }

    /**
     * {@inheritdoc}
     */
    public function get(string $id, string $locale): ChangesetInterface
    {
        $result = $this->connection->fetchAllAssociative('SELECT *
            FROM #__customizer_changeset AS tm
            INNER JOIN #__customizer_changeset_lang AS tl
                ON (tm.id = tl.customizer_changeset_id)
            WHERE tm.id = :id  AND tl.locale = :locale
            LIMIT 1', [
            'id'     => $id,
            'locale' => $locale,
        ]);

        if ($result === []) {
            throw new ChangesetNotFoundException(sprintf('Changeset %s not found.', $id));
        }

        return $this->buildChangesetFromDatabaseRow($result[0]);
    }

    /**
     * {@inheritdoc}
     */
    public function save(ChangesetInterface $changeset, string $locale): void
    {
        if ($changeset->isEmpty()) {
            return;
        }

        /**
         * If this is an active changeset saving, we have to:
         * 1. Remove old ACTIVE changeset for this theme.
         * 2. Make copy of the current saving Changeset with New ID and make it Active.
         * 3. Current edited changeset will be left in Database for the next savings.
         */
        if ($changeset->getType() === ChangesetTypeEnum::ACTIVE) {
            $oldActive = $this->getActiveChangeset($changeset->getTheme(), $locale);

            if ($oldActive && $oldActive->getId() !== $changeset->getId()) {
                $this->remove($oldActive);
            }

            $newId = $this->changesetFactory->factory()->getId();

            $this->makeCopyOf($changeset->getId(), $newId);

            $this->connection->update('#__customizer_changeset', [
                'type'       => $changeset->getType(),
                'author_id'  => $changeset->getAuthorId(),
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => null,
            ], [
                'id' => $newId,
            ]);

            $this->connection->update('#__customizer_changeset_lang', [
                'autogenerated_locale' => 0,
            ], [
                'customizer_changeset_id' => $newId,
            ]);
        } else {
            $this->persist($changeset, $locale);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function remove(ChangesetInterface $changeset)
    {
        $this->connection->delete('#__customizer_changeset', ['id' => $changeset->getId()]);
        $this->connection->delete('#__customizer_changeset_lang', ['customizer_changeset_id' => $changeset->getId()]);
    }

    /**
     * @param ChangesetInterface $changeset
     */
    private function persist(ChangesetInterface $changeset, string $locale): void
    {
        $payloadMain = json_encode($changeset->getAllNotMultilingual());
        $payloadLang = json_encode($changeset->getAllMultilingual());

        if ($this->getRow($changeset->getId(), $locale) === []) {
            $this->connection->insert('#__customizer_changeset', [
                'id'         => $changeset->getId(),
                'type'       => $changeset->getType(),
                'theme'      => $changeset->getTheme(),
                'author_id'  => $changeset->getAuthorId(),
                'created_at' => date('Y-m-d H:i:s'),
                'payload'    => $payloadMain,
            ]);

            foreach ($this->getAvailableLocales() as $loc) {
                $this->connection->insert('#__customizer_changeset_lang', [
                    'customizer_changeset_id' => $changeset->getId(),
                    'payload_localized' => $payloadLang,
                    'autogenerated_locale' => 1,
                    'locale' => $loc,
                ]);
            }

            $this->connection->update('#__customizer_changeset_lang', [
                'autogenerated_locale' => 0,
            ], [
                'customizer_changeset_id' => $changeset->getId(),
                'locale' => $locale,
            ]);
        } else {
            $this->connection->update('#__customizer_changeset', [
                'updated_at' => date('Y-m-d H:i:s'),
                'payload'    => $payloadMain,
            ], [
                'id'    => $changeset->getId(),
                'type'  => $changeset->getType(),
                'theme' => $changeset->getTheme(),
            ]);

            $this->connection->update('#__customizer_changeset_lang', [
                'payload_localized' => $payloadLang,
                'autogenerated_locale' => 0,
            ], [
                'customizer_changeset_id' => $changeset->getId(),
                'locale' => $locale,
            ]);

            /**
             * If updated locale is a default locale, we must update
             * all autogenerated locale rows for this chanegset.
             */
            if ($locale === $this->getDefaultLocale()) {
                $this->connection->update('#__customizer_changeset_lang', [
                    'payload_localized' => $payloadLang,
                ], [
                    'customizer_changeset_id' => $changeset->getId(),
                    'autogenerated_locale' => 1,
                ]);
            }
        }
    }

    /**
     * @param string $currentId
     * @param string $newId
     */
    private function makeCopyOf(string $currentId, string $newId): void
    {
        $this->connection->executeUpdate('INSERT INTO #__customizer_changeset
            (id, theme, type, created_at, updated_at, payload, website_id)
            SELECT :newId AS id, theme, `type`, created_at, updated_at, payload, :websiteId AS website_id
            FROM #__customizer_changeset
            WHERE id = :currentId
            LIMIT 1', [
            'currentId' => $currentId,
            'newId'     => $newId,
        ]);

        $this->connection->executeUpdate('INSERT INTO #__customizer_changeset_lang
            (customizer_changeset_id, locale, autogenerated_locale, payload_localized)
            SELECT :newId AS customizer_changeset_id, locale, `autogenerated_locale`, payload_localized
            FROM #__customizer_changeset_lang
            WHERE customizer_changeset_id = :currentId', [
            'currentId' => $currentId,
            'newId'     => $newId,
        ]);
    }

    /**
     * @param array $row
     *
     * @return ChangesetInterface
     */
    private function buildChangesetFromDatabaseRow(array $row): ChangesetInterface
    {
        $changeset = $this->changesetFactory->factory($row['id']);
        $changeset->setType($row['type']);
        $changeset->setTheme($row['theme']);
        $changeset->setAuthorId($row['author_id'] ?? '');
        $changeset->setAutogeneratedLocale((bool) $row['autogenerated_locale']);

        if ($row['payload']) {
            $changeset->mergeArray(json_decode($row['payload'], true));
        }
        if ($row['payload_localized']) {
            $changeset->mergeArray(json_decode($row['payload_localized'], true));
        }

        return $changeset;
    }

    private function getRow(string $id, string $locale): array
    {
        $result = $this->connection->fetchAllAssociative('SELECT id FROM #__customizer_changeset WHERE id = :id LIMIT 1', [
            'id' => $id
        ]);

        return $result[0] ?? [];
    }

    private function getDefaultLocale(): string
    {
        return $this->currentWebsite->getDefaultLocale()->getCode();
    }

    /**
     * @return array
     */
    protected function getAvailableLocales(): array
    {
        return array_map(function ($locale) {
            return $locale->getCode();
        }, $this->currentWebsite->getLocales());
    }
}
