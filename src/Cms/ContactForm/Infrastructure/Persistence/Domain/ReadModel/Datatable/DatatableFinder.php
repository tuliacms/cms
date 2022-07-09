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
    public function getColumns(): array
    {
        return [
            'id' => [
                'selector' => 'tm.id',
                'type' => 'uuid',
                'label' => 'ID',
            ],
            'name' => [
                'selector' => 'COALESCE(tl.name, tm.name)',
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

        if (false === $context->isDefaultLocale()) {
            $filters['translated'] = [
                'label' => 'translated',
                'type' => 'yes_no',
                'selector' => 'IF(ISNULL(tl.name), 0, 1)'
            ];
        }

        return $filters;
    }

    /**
     * {@inheritdoc}
     */
    public function prepareQueryBuilder(QueryBuilder $queryBuilder, FinderContext $context): QueryBuilder
    {
        $queryBuilder
            ->from('#__form', 'tm')
            ->leftJoin('tm', '#__form_lang', 'tl', 'tm.id = tl.form_id AND tl.locale = :locale')
            ->setParameter('locale', $context->locale, PDO::PARAM_STR)
        ;

        if (false === $context->isDefaultLocale()) {
            $queryBuilder->select('IF(ISNULL(tl.name), 0, 1) AS translated');
        }

        return $queryBuilder;
    }

    /**
     * {@inheritdoc}
     */
    public function buildActions(array $row): array
    {
        return [
            'main' => '@backend/forms/parts/datatable/links/edit-link.tpl',
            'delete' => '@backend/forms/parts/datatable/links/delete-link.tpl',
        ];
    }
}
