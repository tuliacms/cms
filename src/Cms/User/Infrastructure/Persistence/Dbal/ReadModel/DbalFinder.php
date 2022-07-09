<?php

declare(strict_types=1);

namespace Tulia\Cms\User\Infrastructure\Persistence\Dbal\ReadModel;

use Doctrine\DBAL\Connection;
use Tulia\Cms\Content\Attributes\Domain\ReadModel\Service\AttributesFinder;
use Tulia\Cms\Shared\Infrastructure\Persistence\Domain\ReadModel\Finder\AbstractFinder;
use Tulia\Cms\Shared\Infrastructure\Persistence\Domain\ReadModel\Finder\Query\QueryInterface;
use Tulia\Cms\User\Domain\ReadModel\Finder\UserFinderInterface;

/**
 * @author Adam Banaszkiewicz
 */
class DbalFinder extends AbstractFinder implements UserFinderInterface
{

    public function __construct(
        private Connection $connection,
        private AttributesFinder $metadataFinder
    ) {
    }

    public function getAlias(): string
    {
        return 'user';
    }

    public function createQuery(): QueryInterface
    {
        return new DbalFinderQuery(
            $this->connection->createQueryBuilder(),
            $this->metadataFinder
        );
    }
}
