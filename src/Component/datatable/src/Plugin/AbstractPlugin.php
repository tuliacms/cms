<?php

declare(strict_types=1);

namespace Tulia\Component\Datatable\Plugin;

use Doctrine\DBAL\Query\QueryBuilder;
use Tulia\Component\Datatable\Finder\FinderContext;

/**
 * @author Adam Banaszkiewicz
 */
abstract class AbstractPlugin implements PluginInterface
{
    abstract public function supports(string $configurationKey): bool;

    public function prepareQueryBuilder(QueryBuilder $queryBuilder, FinderContext $context): QueryBuilder
    {
        return $queryBuilder;
    }

    public function buildActions(array $row): array
    {
        return [];
    }

    public function getFilters(FinderContext $context): array
    {
        return [];
    }

    public function getColumns(): array
    {
        return [];
    }

    public function prepareResult(array $result): array
    {
        return $result;
    }
}
