<?php

declare(strict_types=1);

namespace Tulia\Cms\Node\Infrastructure\Persistence\Doctrine\Dbal\WriteModel;

use Doctrine\DBAL\Connection;
use Symfony\Component\Uid\Uuid;
use Tulia\Cms\Node\Domain\WriteModel\Service\NodeByPurposeFinderInterface;

/**
 * @author Adam Banaszkiewicz
 */
class DbalNodeByPurposeFinder implements NodeByPurposeFinderInterface
{
    public function __construct(
        private readonly Connection $connection,
    ) {
    }

    public function countOtherNodesWithPurpose(string $localNode, string $websiteId, string $purpose): int
    {
        return $this->connection->fetchOne('
            SELECT COUNT(tm.id) AS `count`
            FROM #__node AS tm
            INNER JOIN #__node_has_purpose AS tnhf
                ON tm.id = tnhf.node_id AND tnhf.purpose = :purpose
            WHERE
                tm.id != :nodeId AND tm.website_id = :websiteId', [
            'websiteId' => Uuid::fromString($websiteId)->toBinary(),
            'purpose' => $purpose,
            'nodeId' => Uuid::fromString($localNode)->toBinary(),
        ], [
            'flags' => Connection::PARAM_STR_ARRAY,
            'websiteId' => \PDO::PARAM_STR,
            'nodeId' => \PDO::PARAM_STR,
        ]);
    }
}
