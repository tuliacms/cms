<?php

declare(strict_types=1);

namespace Tulia\Cms\Options\Infrastructure\Persistence\Doctrine\Dbal;

use Doctrine\DBAL\Connection;
use Symfony\Component\Uid\Uuid;
use Tulia\Cms\Options\Domain\WriteModel\Query\ExistingOptionsQueryInterface;

/**
 * @author Adam Banaszkiewicz
 */
final class DbalExistingOptionsQuery implements ExistingOptionsQueryInterface
{
    public function __construct(
        private readonly Connection $connection
    ) {
    }

    public function collectNames(string $websiteId): array
    {
        return $this->connection->fetchFirstColumn('SELECT name FROM #__option WHERE website_id = :id', [
            'id' => Uuid::fromString($websiteId)->toBinary()
        ]);
    }
}
