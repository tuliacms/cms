<?php

declare(strict_types=1);

namespace Tulia\Cms\Node\Infrastructure\Persistence\Dbal\WriteModel;

use Tulia\Cms\Node\Domain\WriteModel\Service\NodeByPurposeFinderInterface;
use Tulia\Cms\Shared\Infrastructure\Persistence\Doctrine\DBAL\ConnectionInterface;

/**
 * @author Adam Banaszkiewicz
 */
class DbalNodeByPurposeFinder implements NodeByPurposeFinderInterface
{
    public function __construct(
        private ConnectionInterface $connection
    ) {
    }

    public function countOtherNodesWithPurpose(string $localNode, string $purpose, string $websiteId): int
    {
        return $this->connection->fetchOne('
            SELECT COUNT(tm.id) AS count
            FROM #__node AS tm
            INNER JOIN #__node_has_purpose AS tnhf
                ON tm.id = tnhf.node_id AND tnhf.purpose = :purpose
            WHERE
                tm.id != :nodeId AND tm.website_id = :websiteId', [
            'purpose' => $purpose,
            'nodeId' => $localNode,
            'websiteId' => $websiteId,
        ], [
            'flags' => ConnectionInterface::PARAM_ARRAY_STR,
            'nodeId' => ConnectionInterface::PARAM_STR,
            'websiteId' => ConnectionInterface::PARAM_STR,
        ]);
    }
}
