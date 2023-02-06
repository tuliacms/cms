<?php

declare(strict_types=1);

namespace Tulia\Cms\Menu\Infrastructure\Persistence\Domain\ReadModel;

use Doctrine\DBAL\Connection;
use Symfony\Component\Uid\Uuid;
use Tulia\Cms\Menu\Domain\ReadModel\Query\MenuExportCollectorInterface;

/**
 * @author Adam Banaszkiewicz
 */
final class DbalMenuExportCollector implements MenuExportCollectorInterface
{
    public function __construct(
        private readonly Connection $connection,
    ) {
    }

    public function collect(string $websiteId): array
    {
        return $this->connection->fetchAllAssociative(
            'SELECT BIN_TO_UUID(tm.id) AS id, tm.name
            FROM #__menu AS tm
            WHERE tm.website_id = :website_id',
            [
                'website_id' => Uuid::fromString($websiteId)->toBinary(),
            ]
        );
    }
}
