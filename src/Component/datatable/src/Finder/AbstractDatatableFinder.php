<?php

declare(strict_types=1);

namespace Tulia\Component\Datatable\Finder;

use Tulia\Cms\Shared\Infrastructure\Persistence\Doctrine\DBAL\ConnectionInterface;
use Tulia\Cms\Shared\Infrastructure\Persistence\Doctrine\DBAL\Query\QueryBuilder;

/**
 * @author Adam Banaszkiewicz
 */
abstract class AbstractDatatableFinder implements FinderInterface
{
    protected ConnectionInterface $connection;

    public function __construct(ConnectionInterface $connection)
    {
        $this->connection = $connection;
    }

    abstract public function getConfigurationKey(): string;
    abstract public function getColumns(): array;
    abstract public function prepareQueryBuilder(QueryBuilder $queryBuilder, FinderContext $context): QueryBuilder;

    public function getQueryBuilder(): QueryBuilder
    {
        return $this->connection->createQueryBuilder();
    }

    public function buildActions(array $row): array
    {
        return [];
    }

    public function getFilters(FinderContext $context): array
    {
        return [];
    }

    public function prepareResult(array $result): array
    {
        return $result;
    }

    public function fetchAllAssociative(QueryBuilder $queryBuilder): array
    {
        return $queryBuilder->execute()->fetchAllAssociative();
    }
}
