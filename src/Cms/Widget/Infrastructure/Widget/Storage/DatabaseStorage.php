<?php

declare(strict_types=1);

namespace Tulia\Cms\Widget\Infrastructure\Widget\Storage;

use Doctrine\DBAL\Connection;
use Tulia\Cms\Widget\Domain\Catalog\Storage\StorageInterface;
use Tulia\Cms\Widget\Infrastructure\Persistence\Domain\ReadModel\Finder\DbalWidgetAttributesFinder;

/**
 * @author Adam Banaszkiewicz
 */
class DatabaseStorage implements StorageInterface
{
    private static array $cache = [];

    public function __construct(
        private Connection $connection,
        private readonly DbalWidgetAttributesFinder $attributesFinder
    ) {
    }

    /**
     * {@inheritdoc}
     */
    public function all(?string $space, string $locale): array
    {
        if (isset(static::$cache['all'])) {
            return static::$cache['all'];
        }

        static::$cache['all'] = $this->connection->fetchAllAssociative('
            SELECT
                tm.*,
                BIN_TO_UUID(tm.id) AS id,
                tl.title,
                tl.locale,
                tl.visibility
            FROM #__widget AS tm
            LEFT JOIN #__widget_translation AS tl
                ON tl.widget_id = tm.id AND tl.locale = :locale', [
            'locale' => $locale,
        ]);

        static::$cache['all'] = $this->fetchAttributes(static::$cache['all']);

        return static::$cache['all'];
    }

    /**
     * {@inheritdoc}
     */
    public function findById(string $id, string $locale): ?array
    {
        if (isset(static::$cache[$id])) {
            return static::$cache[$id];
        }

        $result = $this->connection->fetchAllAssociative('
            SELECT
                tm.*,
                BIN_TO_UUID(tm.id) AS id,
                tl.title,
                tl.locale,
                tl.visibility
            FROM #__widget AS tm
            LEFT JOIN #__widget_translation AS tl
                ON tl.widget_id = tm.id AND tl.locale = :locale
            WHERE tm.id = :id
            LIMIT 1', [
            'locale' => $locale,
            'id' => $id,
        ]);

        if (isset($result[0]['id'])) {
            static::$cache[$id] = $this->fetchAttributes($result)[0];

            return static::$cache[$id] = $result[0];
        }

        return null;
    }

    public function findBySpace(string $space, string $locale): array
    {
        if (isset(static::$cache[$space])) {
            return static::$cache[$space];
        }

        static::$cache[$space] = $this->connection->fetchAllAssociative('
            SELECT
                tm.*,
                BIN_TO_UUID(tm.id) AS id,
                tl.title,
                tl.locale,
                tl.visibility
            FROM #__widget AS tm
            LEFT JOIN #__widget_translation AS tl
                ON tl.widget_id = tm.id AND tl.locale = :locale
            WHERE tm.space = :space
        ', [
            'locale' => $locale,
            'space' => $space,
        ]);

        static::$cache[$space] = $this->fetchAttributes(static::$cache[$space]);

        return static::$cache[$space];
    }

    private function fetchAttributes(array $rows): array
    {
        foreach ($rows as $key => $row) {
            $rows[$key]['attributes'] = $this->attributesFinder->find($row['id'], $row['locale']);
        }

        return $rows;
    }
}
