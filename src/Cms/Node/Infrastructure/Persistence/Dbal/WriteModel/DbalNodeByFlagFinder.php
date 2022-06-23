<?php

declare(strict_types=1);

namespace Tulia\Cms\Node\Infrastructure\Persistence\Dbal\WriteModel;

use Tulia\Cms\Node\Domain\WriteModel\Service\NodeByFlagFinderInterface;
use Tulia\Cms\Shared\Infrastructure\Persistence\Doctrine\DBAL\ConnectionInterface;

/**
 * @author Adam Banaszkiewicz
 */
class DbalNodeByFlagFinder implements NodeByFlagFinderInterface
{
    private ConnectionInterface $connection;

    public function __construct(ConnectionInterface $connection)
    {
        $this->connection = $connection;
    }

    public function findOtherNodesWithFlags(string $localNode, array $purposes, string $websiteId): array
    {
        return $this->connection->fetchAllAssociative('SELECT tm.id, tnhf.purpose
            FROM #__node AS tm
            INNER JOIN #__node_has_purpose AS tnhf
                ON tm.id = tnhf.node_id AND tnhf.purpose IN (:purposes)
            WHERE
                tm.id != :nodeId AND tm.website_id = :websiteId', [
            'purposes' => $purposes,
            'nodeId' => $localNode,
            'websiteId' => $websiteId,
        ], [
            'flags' => ConnectionInterface::PARAM_ARRAY_STR,
            'nodeId' => ConnectionInterface::PARAM_STR,
            'websiteId' => ConnectionInterface::PARAM_STR,
        ]);
    }
}
