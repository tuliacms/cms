<?php

declare(strict_types=1);

namespace Tulia\Cms\User\Infrastructure\Persistence\Dbal\ReadModel;

use Doctrine\DBAL\Connection;
use Tulia\Cms\User\Domain\ReadModel\Query\UserSearchCollectorInterface;

/**
 * @author Adam Banaszkiewicz
 */
final class DbalUserSearchCollector implements UserSearchCollectorInterface
{
    public function __construct(
        private Connection $connection
    ) {
    }

    public function collectDocuments(int $offset, int $limit): array
    {
        return $this->connection->fetchAllAssociative('
            SELECT BIN_TO_UUID(id) AS id, name, email
            FROM #__user
            ORDER BY id ASC
            LIMIT :offset, :limit',
            [
                'limit' => $limit,
                'offset' => $offset,
            ],
            [
                'limit' => \PDO::PARAM_INT,
                'offset' => \PDO::PARAM_INT,
            ]
        );
    }

    public function countDocuments(): int
    {
        return (int) $this->connection->fetchNumeric('SELECT COUNT(id) FROM #__user');
    }
}
