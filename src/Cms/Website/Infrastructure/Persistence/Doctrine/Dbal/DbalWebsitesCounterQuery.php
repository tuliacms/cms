<?php

declare(strict_types=1);

namespace Tulia\Cms\Website\Infrastructure\Persistence\Doctrine\Dbal;

use Doctrine\DBAL\Connection;
use Symfony\Component\Uid\Uuid;
use Tulia\Cms\Website\Domain\WriteModel\Query\WebsitesCounterQueryInterface;

/**
 * @author Adam Banaszkiewicz
 */
final class DbalWebsitesCounterQuery implements WebsitesCounterQueryInterface
{
    public function __construct(
        private readonly Connection $connection
    ) {
    }

    public function countActiveOnesExcept(string $except): int
    {
        return (int) $this->connection->fetchOne(
            'SELECT COUNT(id) AS count FROM #__website WHERE active = 1 AND id != :id',
            ['id' => Uuid::fromString($except)->toBinary()]
        );
    }
}
