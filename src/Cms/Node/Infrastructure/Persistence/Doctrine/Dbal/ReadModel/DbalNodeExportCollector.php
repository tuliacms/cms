<?php

declare(strict_types=1);

namespace Tulia\Cms\Node\Infrastructure\Persistence\Doctrine\Dbal\ReadModel;

use Doctrine\DBAL\Connection;
use Symfony\Component\Uid\Uuid;
use Tulia\Cms\Node\Domain\ReadModel\Query\NodeExportCollectorInterface;

/**
 * @author Adam Banaszkiewicz
 */
final class DbalNodeExportCollector implements NodeExportCollectorInterface
{
    public function __construct(
        private readonly Connection $connection,
    ) {
    }

    public function collect(string $websiteId, string $locale): array
    {
        return $this->connection->fetchAllAssociative(
            'SELECT BIN_TO_UUID(tm.id) AS id, tl.title, tm.type
            FROM #__node AS tm
            INNER JOIN #__node_translation AS tl
                ON tm.id = tl.node_id AND tl.locale = :locale
            WHERE tm.website_id = :website_id',
            [
                'locale' => $locale,
                'website_id' => Uuid::fromString($websiteId)->toBinary(),
            ]
        );
    }
}
