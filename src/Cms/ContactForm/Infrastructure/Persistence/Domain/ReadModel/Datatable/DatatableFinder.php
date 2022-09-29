<?php

declare(strict_types=1);

namespace Tulia\Cms\ContactForm\Infrastructure\Persistence\Domain\ReadModel\Datatable;

use Doctrine\DBAL\Query\QueryBuilder;
use PDO;
use Tulia\Component\Datatable\Finder\AbstractDatatableFinder;
use Tulia\Component\Datatable\Finder\FinderContext;

/**
 * @author Adam Banaszkiewicz
 */
class DatatableFinder extends AbstractDatatableFinder
{
    /**
     * {@inheritdoc}
     */
    public function getConfigurationKey(): string
    {
        return __CLASS__;
    }

    /**
     * {@inheritdoc}
     */
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
                'view' => '@backend/forms/parts/datatable/name.tpl',
            ],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function getFilters(FinderContext $context): array
    {
        $filters = [
            'name' => [
                'label' => 'name',
                'type' => 'text',
            ],
        ];

        return $filters;
    }

    /**
     * {@inheritdoc}
     */
    public function prepareQueryBuilder(QueryBuilder $queryBuilder, FinderContext $context): QueryBuilder
    {
        $queryBuilder
            ->from('#__form', 'tm')
            ->setParameter('locale', $context->locale, PDO::PARAM_STR)
        ;

        return $queryBuilder;
    }

    /**
     * {@inheritdoc}
     */
    public function buildActions(FinderContext $context, array $row): array
    {
        return [
            'main' => '@backend/forms/parts/datatable/links/edit-link.tpl',
            'delete' => '@backend/forms/parts/datatable/links/delete-link.tpl',
        ];
    }
}
