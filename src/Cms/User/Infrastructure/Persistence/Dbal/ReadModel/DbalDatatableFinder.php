<?php

declare(strict_types=1);

namespace Tulia\Cms\User\Infrastructure\Persistence\Dbal\ReadModel;

use Doctrine\DBAL\Query\QueryBuilder;
use Tulia\Component\Datatable\Finder\AbstractDatatableFinder;
use Tulia\Component\Datatable\Finder\FinderContext;

/**
 * @author Adam Banaszkiewicz
 */
class DbalDatatableFinder extends AbstractDatatableFinder
{
    public function getColumns(FinderContext $context): array
    {
        return [
            'id' => [
                'selector' => 'BIN_TO_UUID(tm.id)',
                'type' => 'uuid',
                'label' => 'ID',
            ],
            'email' => [
                'selector' => 'tm.email',
                'label' => 'email',
                'sortable' => true,
                'html_attr' => ['class' => 'col-title'],
                'view' => '@backend/user/user/parts/datatable/name.tpl',
            ],
            'name' => [
                'selector' => 'tm.name',
                'label' => 'name',
                'sortable' => true,
            ],
            'enabled' => [
                'selector' => 'tm.enabled',
                'label' => 'enabled',
                'sortable' => true,
                'value_translation' => [
                    1 => 'Enabled',
                    0 => 'Disabled',
                ],
                'value_class' => [
                    '1' => 'text-success',
                ],
            ],
        ];
    }

    public function getFilters(FinderContext $context): array
    {
        return [
            'email' => [
                'label' => 'email',
                'type' => 'text',
                'selector' => 'tm.email',
            ],
            'name' => [
                'label' => 'name',
                'type' => 'text',
                'selector' => 'tm.name',
            ],
            'enabled' => [
                'label' => 'enabled',
                'type' => 'yes_no',
            ],
        ];
    }

    public function prepareQueryBuilder(QueryBuilder $queryBuilder, FinderContext $context): QueryBuilder
    {
        $queryBuilder
            ->select('tm.email, tm.name')
            ->from('#__user', 'tm')
        ;

        return $queryBuilder;
    }

    public function buildActions(FinderContext $context, array $row): array
    {
        return [
            'main' => '@backend/user/user/parts/datatable/links/edit-link.tpl',
            'delete' => '@backend/user/user/parts/datatable/links/delete-link.tpl',
        ];
    }
}
