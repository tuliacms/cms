<?php

declare(strict_types=1);

namespace Tulia\Cms\Widget\Infrastructure\Widget\Storage;

use Doctrine\DBAL\Connection;
use Symfony\Component\Uid\Uuid;
use Tulia\Cms\Widget\Domain\Catalog\Storage\StorageInterface;
use Tulia\Cms\Widget\Infrastructure\Persistence\Domain\ReadModel\Finder\DbalWidgetAttributesFinder;

/**
 * @author Adam Banaszkiewicz
 */
class DatabaseStorage implements StorageInterface
{
    private static array $cache = [];

    public function __construct(
        private readonly Connection $connection,
        private readonly DbalWidgetAttributesFinder $attributesFinder
    ) {
    }

    /**
     * {@inheritdoc}
     */
    public function all(?string $space, string $websiteId, string $locale): array
    {
        if (isset(static::$cache[$websiteId]['all'])) {
            return static::$cache[$websiteId]['all'];
        }

        static::$cache[$websiteId]['all'] = $this->connection->fetchAllAssociative('
            SELECT
                tm.*,
                BIN_TO_UUID(tm.id) AS id,
                tl.title,
                tl.locale,
                tl.visibility
            FROM #__widget AS tm
            INNER JOIN #__widget_translation AS tl
                ON tl.widget_id = tm.id AND tm.website_id = :websiteId AND tl.locale = :locale', [
            'locale' => $locale,
            'websiteId' => Uuid::fromString($websiteId)->toBinary(),
        ]);

        static::$cache[$websiteId]['all'] = $this->fetchAttributes(static::$cache[$websiteId]['all']);

        return static::$cache[$websiteId]['all'];
    }

    /**
     * {@inheritdoc}
     */
    public function findById(string $id, string $websiteId, string $locale): ?array
    {
        if (isset(static::$cache[$websiteId][$id])) {
            return static::$cache[$websiteId][$id];
        }

        $result = $this->connection->fetchAllAssociative('
            SELECT
                tm.*,
                BIN_TO_UUID(tm.id) AS id,
                tl.title,
                tl.locale,
                tl.visibility
            FROM #__widget AS tm
            INNER JOIN #__widget_translation AS tl
                ON tl.widget_id = tm.id AND tm.website_id = :websiteId AND tl.locale = :locale
            WHERE tm.id = :id
            LIMIT 1', [
            'locale' => $locale,
            'websiteId' => Uuid::fromString($websiteId)->toBinary(),
            'id' => $id,
        ]);

        if (isset($result[0]['id'])) {
            static::$cache[$websiteId][$id] = $this->fetchAttributes($result)[0];

            return static::$cache[$websiteId][$id] = $result[0];
        }

        return null;
    }

    public function findBySpace(string $space, string $websiteId, string $locale): array
    {
        if (isset(static::$cache[$websiteId][$space])) {
            return static::$cache[$websiteId][$space];
        }

        static::$cache[$websiteId][$space] = $this->connection->fetchAllAssociative('
            SELECT
                tm.*,
                BIN_TO_UUID(tm.id) AS id,
                tl.title,
                tl.locale,
                tl.visibility
            FROM #__widget AS tm
            INNER JOIN #__widget_translation AS tl
                ON tl.widget_id = tm.id AND tm.website_id = :websiteId AND tl.locale = :locale
            WHERE tm.space = :space
        ', [
            'locale' => $locale,
            'websiteId' => Uuid::fromString($websiteId)->toBinary(),
            'space' => $space,
        ]);

        static::$cache[$websiteId][$space] = $this->fetchAttributes(static::$cache[$websiteId][$space]);

        return static::$cache[$websiteId][$space];
    }

    private function fetchAttributes(array $rows): array
    {
        foreach ($rows as $key => $row) {
            $rows[$key]['attributes'] = $this->attributesFinder->find($row['id'], $row['locale']);
        }

        return $rows;
    }
}
