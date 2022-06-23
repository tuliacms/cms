<?php

declare(strict_types=1);

namespace Tulia\Cms\Node\Infrastructure\Persistence\Dbal\WriteModel;

use Tulia\Cms\Node\Domain\WriteModel\Service\NodeChildrenDetectorInterface;
use Tulia\Cms\Shared\Infrastructure\Persistence\Doctrine\DBAL\ConnectionInterface;

/**
 * @author Adam Banaszkiewicz
 */
final class DbalNodeChildrenDetector implements NodeChildrenDetectorInterface
{
    public function __construct(
        private ConnectionInterface $connection
    ) {
    }

    public function hasChildren(string $nodeId): bool
    {
        return (bool) $this->connection->fetchOne(
            'SELECT COUNT(id) AS count FROM #__node WHERE parent_id = :parent_id LIMIT 1',
            ['parent_id' => $nodeId],
            ['parent_id' => \PDO::PARAM_STR]
        );
    }
}
