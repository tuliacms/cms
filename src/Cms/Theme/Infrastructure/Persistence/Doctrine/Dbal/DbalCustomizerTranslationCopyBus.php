<?php

declare(strict_types=1);

namespace Tulia\Cms\Theme\Infrastructure\Persistence\Doctrine\Dbal;

use Doctrine\DBAL\Connection;
use Symfony\Component\Uid\Uuid;
use Tulia\Cms\Website\Domain\WriteModel\Service\TranslationCopyMachine\CopyBusInterface;

/**
 * @author Adam Banaszkiewicz
 * @final
 * @lazy
 */
class DbalCustomizerTranslationCopyBus implements CopyBusInterface
{
    public function __construct(
        private readonly Connection $connection,
    ) {
    }

    public function count(string $websiteId, string $defaultLocale): int
    {
        return (int) $this->connection->fetchOne('
            SELECT COUNT(ct.id)
            FROM #__theme_customization_changeset_translation AS ct
            INNER JOIN #__theme_customization_changeset AS c
                ON c.id = ct.changeset_id
            WHERE c.website_id = :website AND ct.locale = :locale', [
            'website' => Uuid::fromString($websiteId)->toBinary(),
            'locale' => $defaultLocale,
        ]);
    }

    public function copy(string $websiteId, string $defaultLocale, string $targetLocale, int $offset, int $limit): int
    {
        $idList = $this->getChangesetIdListPart($websiteId, $offset, $limit);
        $source = $this->getTranslationsSource($idList, $defaultLocale);

        foreach ($source as $item) {
            $id = Uuid::v4()->toBinary();

            $this->connection->insert('#__theme_customization_changeset_translation', [
                'id' => $id,
                'locale' => $targetLocale,
                'changeset_id' => $item['changeset_id'],
                'payload' => $item['payload'],
            ]);
        }

        return count($source);
    }

    public function delete(string $websiteId, string $defaultLocale, string $targetLocale, int $offset, int $limit): int
    {
        $idList = $this->getChangesetIdListPart($websiteId, $offset, $limit);
        $translationsIdList = array_column($this->getTranslationsSource($idList, $targetLocale), 'id');

        $this->connection->executeStatement(
            'DELETE FROM #__theme_customization_changeset_translation WHERE id IN (:id)',
            ['id' => $translationsIdList],
            ['id' => Connection::PARAM_STR_ARRAY]
        );

        return count($translationsIdList);
    }

    private function getChangesetIdListPart(string $websiteId, int $offset, int $limit): array
    {
        return $this->connection->fetchFirstColumn('
            SELECT id
            FROM #__theme_customization_changeset
            WHERE website_id = :website
            ORDER BY id ASC
            LIMIT :offset, :limit', [
            'website' => Uuid::fromString($websiteId)->toBinary(),
            'offset' => $offset,
            'limit' => $limit,
        ], [
            'offset' => \PDO::PARAM_INT,
            'limit' => \PDO::PARAM_INT,
        ]);
    }

    private function getTranslationsSource(array $nodeIdList, string $locale): array
    {
        return $this->connection->fetchAllAssociative('
            SELECT id, changeset_id, locale, payload
            FROM #__theme_customization_changeset_translation
            WHERE changeset_id IN (:ids) AND locale = :locale', [
            'locale' => $locale,
            'ids' => $nodeIdList,
        ], [
            'ids' => Connection::PARAM_STR_ARRAY,
        ]);
    }
}
