<?php

declare(strict_types=1);

namespace Tulia\Cms\Widget\Infrastructure\Persistence\Doctrine\Dbal;

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
        private readonly Connection $connection,
        private readonly DbalWidgetAttributesFinder $attributesFinder
    ) {
    }

    public function getAlias(): string
    {
        return 'widget';
    }

    public function createQuery(): QueryInterface
    {
        return new DbalQuery($this->connection->createQueryBuilder(), $this->attributesFinder);
    }
}
