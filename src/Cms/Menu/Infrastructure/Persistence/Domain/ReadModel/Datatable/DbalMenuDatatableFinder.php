<?php

declare(strict_types=1);

namespace Tulia\Cms\Menu\Infrastructure\Persistence\Domain\ReadModel\Datatable;

use Doctrine\DBAL\Query\QueryBuilder;
use PDO;
use Symfony\Component\Uid\Uuid;
use Tulia\Cms\Menu\Domain\ReadModel\Datatable\MenuDatatableFinderInterface;
use Tulia\Component\Datatable\Finder\AbstractDatatableFinder;
use Tulia\Component\Datatable\Finder\FinderContext;

/**
 * @author Adam Banaszkiewicz
 */
class DbalMenuDatatableFinder extends AbstractDatatableFinder implements MenuDatatableFinderInterface
{
    public function getColumns(FinderContext $context): array
    {
        return [
            'id' => [
                'selector' => 'BIN_TO_UUID(tm.id)',
                'type' => 'uuid',
                'label' => 'ID',
            ],
            'name' => [
                'selector' => 'tm.name',
                'label' => 'name',
                'sortable' => true,
                'view' => '@backend/menu/menu/parts/datatable/name.tpl',
            ],
        ];
    }

    public function getFilters(FinderContext $context): array
    {
        return [
            'name' => [
                'label' => 'name',
                'type' => 'text',
            ],
        ];
    }

    public function prepareQueryBuilder(QueryBuilder $queryBuilder, FinderContext $context): QueryBuilder
    {
        $queryBuilder
            ->from('#__menu', 'tm')
            ->where('tm.website_id = :website_id')
            ->setParameter('website_id', Uuid::fromString($context['website']->getId())->toBinary(), PDO::PARAM_STR)
        ;

        return $queryBuilder;
    }

    public function buildActions(FinderContext $context, array $row): array
    {
        return [
            'main' => '@backend/menu/menu/parts/datatable/links/edit-link.tpl',
            'items' => '@backend/menu/menu/parts/datatable/links/items-link.tpl',
            'delete' => '@backend/menu/menu/parts/datatable/links/delete-link.tpl',
        ];
    }
}
