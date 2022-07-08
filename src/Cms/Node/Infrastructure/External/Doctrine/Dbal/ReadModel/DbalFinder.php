<?php

declare(strict_types=1);

namespace Tulia\Cms\Node\Infrastructure\External\Doctrine\Dbal\ReadModel;

use Tulia\Cms\Content\Attributes\Domain\ReadModel\Service\AttributesFinder;
use Tulia\Cms\Node\Domain\ReadModel\Finder\NodeFinderInterface;
use Tulia\Cms\Shared\Infrastructure\Persistence\Doctrine\DBAL\ConnectionInterface;
use Tulia\Cms\Shared\Infrastructure\Persistence\Domain\ReadModel\Finder\AbstractFinder;
use Tulia\Cms\Shared\Infrastructure\Persistence\Domain\ReadModel\Finder\Query\QueryInterface;

/**
 * @author Adam Banaszkiewicz
 */
class DbalFinder extends AbstractFinder implements NodeFinderInterface
{
    private ConnectionInterface $connection;
    private AttributesFinder $metadataFinder;

    public function __construct(
        ConnectionInterface $connection,
        AttributesFinder $metadataFinder,
    ) {
        $this->connection = $connection;
        $this->metadataFinder = $metadataFinder;
    }

    public function getAlias(): string
    {
        return 'node';
    }

    public function createQuery(): QueryInterface
    {
        return new DbalFinderQuery(
            $this->connection->createQueryBuilder(),
            $this->metadataFinder
        );
    }
}
