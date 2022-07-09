<?php

declare(strict_types=1);

namespace Tulia\Cms\Menu\Infrastructure\Persistence\Domain\ReadModel\Finder\Query;

use Doctrine\DBAL\Connection;
use Tulia\Cms\Content\Attributes\Domain\ReadModel\Service\AttributesFinder;
use Tulia\Cms\Menu\Domain\ReadModel\Finder\MenuFinderInterface;
use Tulia\Cms\Shared\Infrastructure\Persistence\Domain\ReadModel\Finder\AbstractFinder;
use Tulia\Cms\Shared\Infrastructure\Persistence\Domain\ReadModel\Finder\Query\QueryInterface;

/**
 * @author Adam Banaszkiewicz
 */
class DbalFinder extends AbstractFinder implements MenuFinderInterface
{
    public function __construct(
        private Connection $connection,
        private AttributesFinder $metadataFinder
    ) {
    }

    public function getAlias(): string
    {
        return 'menu';
    }

    public function createQuery(): QueryInterface
    {
        return new DbalQuery($this->connection->createQueryBuilder(), $this->metadataFinder);
    }
}
