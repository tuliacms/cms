<?php

declare(strict_types=1);

namespace Tulia\Cms\ContactForm\Infrastructure\Persistence\Domain\ReadModel\Datatable;

use Doctrine\DBAL\Query\QueryBuilder;
use PDO;
use Symfony\Component\Uid\Uuid;
use Tulia\Component\Datatable\Finder\AbstractDatatableFinder;
use Tulia\Component\Datatable\Finder\FinderContext;

/**
 * @author Adam Banaszkiewicz
 */
class DatatableFinder extends AbstractDatatableFinder
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
                'view' => '@backend/forms/parts/datatable/name.tpl',
            ],
        ];
    }

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

    public function prepareQueryBuilder(QueryBuilder $queryBuilder, FinderContext $context): QueryBuilder
    {
        $queryBuilder
            ->select('tm.name, BIN_TO_UUID(tm.id) AS id, tl.translated')
            ->from('#__form', 'tm')
            ->leftJoin('tm', '#__form_translation', 'tl', 'tm.id = tl.form_id AND tl.locale = :locale')
            ->where('tm.website_id = :website_id')
            ->setParameter('website_id', Uuid::fromString($context['website']->getId())->toBinary(), PDO::PARAM_STR)
            ->setParameter('locale', $context['website']->getLocale()->getCode(), PDO::PARAM_STR)
        ;

        return $queryBuilder;
    }

    public function buildActions(FinderContext $context, array $row): array
    {
        return [
            'main' => '@backend/forms/parts/datatable/links/edit-link.tpl',
            'delete' => '@backend/forms/parts/datatable/links/delete-link.tpl',
        ];
    }
}
