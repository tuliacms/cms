<?php

declare(strict_types=1);

namespace Tulia\Cms\Widget\Infrastructure\Persistence\Domain\ReadModel\Finder;

use Doctrine\DBAL\Connection;
use Tulia\Cms\Shared\Infrastructure\Persistence\Domain\ReadModel\Finder\AbstractFinder;
use Tulia\Cms\Shared\Infrastructure\Persistence\Domain\ReadModel\Finder\Query\QueryInterface;
use Tulia\Cms\Widget\Domain\ReadModel\Finder\WidgetFinderInterface;

/**
 * @author Adam Banaszkiewicz
 */
class DbalFinder extends AbstractFinder implements WidgetFinderInterface
{
    public function __construct(
        private Connection $connection
    ) {
    }

    public function getAlias(): string
    {
        return 'widget';
    }

    public function createQuery(): QueryInterface
    {
        return new DbalQuery($this->connection->createQueryBuilder());
    }
}
