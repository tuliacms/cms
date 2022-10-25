<?php

declare(strict_types=1);

namespace Tulia\Cms\Node\Infrastructure\Persistence\Doctrine\Dbal\WriteModel;

use Doctrine\DBAL\Connection;
use Tulia\Cms\Node\Domain\WriteModel\Service\NodeChildrenDetectorInterface;

/**
 * @author Adam Banaszkiewicz
 */
final class DbalNodeChildrenDetector implements NodeChildrenDetectorInterface
{
    public function __construct(
        private Connection $connection
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
