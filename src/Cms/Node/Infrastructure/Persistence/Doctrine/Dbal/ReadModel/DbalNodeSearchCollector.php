<?php

declare(strict_types=1);

namespace Tulia\Cms\Node\Infrastructure\Persistence\Doctrine\Dbal\ReadModel;

use Doctrine\DBAL\Connection;
use Symfony\Component\Uid\Uuid;
use Tulia\Cms\Node\Domain\ReadModel\Query\NodeSearchCollectorInterface;

/**
 * @author Adam Banaszkiewicz
 */
final class DbalNodeSearchCollector implements NodeSearchCollectorInterface
{
    public function __construct(
        private Connection $connection
    ) {
    }

    public function collectTranslationsOfNode(string $id): array
    {
        return $this->connection->fetchAllAssociative(
            'SELECT BIN_TO_UUID(tm.id) AS id, tm.type, tm.website_id, tl.title, tl.locale
            FROM #__node AS tm
            LEFT JOIN #__node_translation AS tl
                ON tm.id = tl.node_id
            WHERE tm.id = :id AND tl.locale IS NOT NULL',
            ['id' => Uuid::fromString($id)->toBinary()],
            ['id' => \PDO::PARAM_STR]
        );
    }

    public function collectDocumentsOfLocale(string $websiteId, string $locale, int $offset, int $limit): array
    {
        return $this->connection->fetchAllAssociative(
            'SELECT BIN_TO_UUID(tm.id) AS id, tm.type, tm.website_id, tl.title, tl.locale
            FROM #__node AS tm
            INNER JOIN #__node_translation AS tl
                ON tm.id = tl.node_id AND tm.website_id = :websiteId AND tl.locale = :locale
            LIMIT :offset, :limit',
            [
                'limit' => $limit,
                'offset' => $offset,
                'locale' => $locale,
                'websiteId' => Uuid::fromString($websiteId)->toBinary(),
            ],
            [
                'limit' => \PDO::PARAM_INT,
                'offset' => \PDO::PARAM_INT,
                'locale' => \PDO::PARAM_STR,
                'websiteId' => \PDO::PARAM_STR,
            ]
        );
    }

    public function countDocumentsOfLocale(string $websiteId, string $locale): int
    {

    }
}
