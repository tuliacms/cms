<?php

declare(strict_types=1);

namespace Tulia\Component\Datatable\Finder;

use Doctrine\DBAL\Query\QueryBuilder;

/**
 * @author Adam Banaszkiewicz
 */
interface FinderInterface
{
    public function getConfigurationKey(): string;

    public function getColumns(FinderContext $context): array;

    public function getFilters(FinderContext $context): array;

    public function prepareQueryBuilder(QueryBuilder $queryBuilder, FinderContext $context): QueryBuilder;

    public function getQueryBuilder(): QueryBuilder;

    public function buildActions(FinderContext $context, array $row): array;

    public function prepareResult(array $result): array;

    public function fetchAllAssociative(QueryBuilder $queryBuilder): array;
}
