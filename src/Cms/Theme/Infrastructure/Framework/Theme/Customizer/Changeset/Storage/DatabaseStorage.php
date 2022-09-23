<?php

declare(strict_types=1);

namespace Tulia\Cms\Theme\Infrastructure\Framework\Theme\Customizer\Changeset\Storage;

use Doctrine\DBAL\Connection;
use Symfony\Component\Uid\Uuid;
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
        private readonly Connection $connection,
        private readonly ChangesetFactoryInterface $changesetFactory,
    ) {
    }

    public function has(string $id, string $websiteId, string $locale): bool
    {
        // Try...catch detects empty or invalid ID
        try {
            $id = Uuid::fromString($id)->toBinary();
        } catch (\InvalidArgumentException $e) {
            return false;
        }

        $result = $this->connection->fetchAllAssociative('SELECT id FROM #__theme_customization_changeset WHERE id = :id AND website_id = :website_id LIMIT 1', [
            'id' => $id,
            'website_id' => Uuid::fromString($websiteId)->toBinary(),
        ]);

        return isset($result[0]['id']);
    }

    public function getActiveChangeset(string $theme, string $websiteId, string $locale): ?ChangesetInterface
    {
        $result = $this->connection->fetchAllAssociative('
            SELECT
                BIN_TO_UUID(tm.id) AS id,
                tm.type,
                tm.theme,
                tm.payload AS payload_global,
                tl.payload AS payload_multilingual
            FROM #__theme_customization_changeset AS tm
            INNER JOIN #__theme_customization_changeset_translation AS tl
                ON (tm.id = tl.changeset_id)
            WHERE
                tm.theme = :theme
                AND tm.type = :type
                AND tm.website_id = :website_id
                AND tl.locale = :locale
            LIMIT 1', [
            'theme'  => $theme,
            'type'   => ChangesetTypeEnum::ACTIVE,
            'locale' => $locale,
            'website_id' => Uuid::fromString($websiteId)->toBinary(),
        ]);

        if ($result === []) {
            return null;
        }

        return $this->buildChangesetFromDatabaseRow($result[0]);
    }

    public function get(string $id, string $websiteId, string $locale): ChangesetInterface
    {
        $result = $this->connection->fetchAllAssociative('
            SELECT
                BIN_TO_UUID(tm.id) AS id,
                tm.type,
                tm.theme,
                tm.payload AS payload_global,
                tl.payload AS payload_multilingual
            FROM #__theme_customization_changeset AS tm
            INNER JOIN #__theme_customization_changeset_translation AS tl
                ON tm.id = tl.changeset_id
            WHERE
                tm.id = :id
                AND tm.website_id = :website_id
                AND tl.locale = :locale
            LIMIT 1', [
            'id'     => Uuid::fromString($id)->toBinary(),
            'locale' => $locale,
            'website_id' => Uuid::fromString($websiteId)->toBinary(),
        ]);

        if ($result === []) {
            throw new ChangesetNotFoundException(sprintf('Changeset %s not found.', $id));
        }

        return $this->buildChangesetFromDatabaseRow($result[0]);
    }

    private function buildChangesetFromDatabaseRow(array $row): ChangesetInterface
    {
        $changeset = $this->changesetFactory->factory($row['id']);
        $changeset->setType($row['type']);
        $changeset->setTheme($row['theme']);

        if ($row['payload_global']) {
            $changeset->mergeArray(json_decode($row['payload_global'], true));
        }
        if ($row['payload_multilingual']) {
            $changeset->mergeArray(json_decode($row['payload_multilingual'], true));
        }

        return $changeset;
    }
}
