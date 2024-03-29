<?php

declare(strict_types=1);

namespace Tulia\Component\Datatable\Plugin;

use Doctrine\DBAL\Query\QueryBuilder;
use Tulia\Component\Datatable\Finder\FinderContext;

/**
 * @author Adam Banaszkiewicz
 */
interface PluginInterface
{
    public function supports(string $configurationKey): bool;
    public function getColumns(FinderContext $context): array;
    public function prepareQueryBuilder(QueryBuilder $queryBuilder, FinderContext $context): QueryBuilder;
    public function buildActions(FinderContext $context, array $row): array;
    public function getFilters(FinderContext $context): array;
    public function prepareResult(array $result): array;
}
