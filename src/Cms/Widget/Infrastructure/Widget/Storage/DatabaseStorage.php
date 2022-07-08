<?php

declare(strict_types=1);

namespace Tulia\Cms\Widget\Infrastructure\Widget\Storage;

use Tulia\Cms\Shared\Infrastructure\Persistence\Doctrine\DBAL\ConnectionInterface;
use Tulia\Cms\Widget\Domain\Catalog\Storage\StorageInterface;

/**
 * @author Adam Banaszkiewicz
 */
class DatabaseStorage implements StorageInterface
{
    private ConnectionInterface $connection;
    private static array $cache = [];

    public function __construct(ConnectionInterface $connection)
    {
        $this->connection = $connection;
    }

    /**
     * {@inheritdoc}
     */
    public function all(?string $space): array
    {
        if (isset(static::$cache['all'])) {
            return static::$cache['all'];
        }

        return static::$cache['all'] = $this->connection->fetchAllAssociative('
            SELECT
                tm.*,
                COALESCE(tl.title, tm.title) AS title,
                COALESCE(tl.visibility, tm.visibility) AS visibility
            FROM #__widget AS tm
            LEFT JOIN #__widget_lang AS tl
                ON tl.widget_id = tm.id AND tl.locale = :locale', [
            'locale'    => $this->getLocale(),
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function findById(string $id): ?array
    {
        if (isset(static::$cache[$id])) {
            return static::$cache[$id];
        }

        $result = $this->connection->fetchAllAssociative('
            SELECT
                tm.*,
                COALESCE(tl.title, tm.title) AS title,
                COALESCE(tl.visibility, tm.visibility) AS visibility
            FROM #__widget AS tm
            LEFT JOIN #__widget_lang AS tl
                ON tl.widget_id = tm.id AND tl.locale = :locale
            WHERE tm.id = :id
            LIMIT 1', [
            'locale'    => $this->getLocale(),
            'id'        => $id,
        ]);

        return static::$cache[$id] = $result[0] ?? null;
    }

    public function findBySpace(string $space): array
    {
        if (isset(static::$cache[$space])) {
            return static::$cache[$space];
        }

        return static::$cache[$space] = $this->connection->fetchAllAssociative('
            SELECT
                tm.*,
                COALESCE(tl.title, tm.title) AS title,
                COALESCE(tl.visibility, tm.visibility) AS visibility
            FROM #__widget AS tm
            LEFT JOIN #__widget_lang AS tl
                ON tl.widget_id = tm.id AND tl.locale = :locale
            WHERE tm.space = :space
        ', [
            'locale'    => $this->getLocale(),
            'space'     => $space,
        ]);
    }

    private function getLocale(): string
    {
        return $this->currentWebsite->getLocale()->getCode();
    }
}
