<?php

declare(strict_types=1);

namespace Tulia\Cms\Website\Infrastructure\Persistence\Domain\ReadModel\Finder;

use Doctrine\DBAL\Connection;
use Tulia\Cms\Shared\Infrastructure\Persistence\Domain\ReadModel\Finder\AbstractFinder;
use Tulia\Cms\Shared\Infrastructure\Persistence\Domain\ReadModel\Finder\Query\QueryInterface;
use Tulia\Cms\Website\Domain\ReadModel\Finder\WebsiteFinderInterface;

/**
 * @author Adam Banaszkiewicz
 */
class DbalFinder extends AbstractFinder implements WebsiteFinderInterface
{
    public function __construct(
        private Connection $connection
    ) {
    }

    public function getAlias(): string
    {
        return 'website';
    }

    public function createQuery(): QueryInterface
    {
        return new DbalQuery($this->connection->createQueryBuilder());
    }
}
