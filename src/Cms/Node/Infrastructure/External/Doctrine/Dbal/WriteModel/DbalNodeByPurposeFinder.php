<?php

declare(strict_types=1);

namespace Tulia\Cms\Node\Infrastructure\External\Doctrine\Dbal\WriteModel;

use Doctrine\DBAL\Connection;
use Tulia\Cms\Node\Domain\WriteModel\Service\NodeByPurposeFinderInterface;

/**
 * @author Adam Banaszkiewicz
 */
class DbalNodeByPurposeFinder implements NodeByPurposeFinderInterface
{
    public function __construct(
        private Connection $connection
    ) {
    }

    public function countOtherNodesWithPurpose(string $localNode, string $purpose): int
    {
        return $this->connection->fetchOne('
            SELECT COUNT(tm.id) AS count
            FROM #__node AS tm
            INNER JOIN #__node_has_purpose AS tnhf
                ON tm.id = tnhf.node_id AND tnhf.purpose = :purpose
            WHERE
                tm.id != :nodeId', [
            'purpose' => $purpose,
            'nodeId' => $localNode,
        ], [
            'flags' => Connection::PARAM_STR_ARRAY,
            'nodeId' => \PDO::PARAM_STR,
        ]);
    }
}
