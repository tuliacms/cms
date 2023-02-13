<?php

declare(strict_types=1);

namespace Tulia\Cms\Menu\Infrastructure\Persistence\Domain\ReadModel\Datatable;

use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Query\QueryBuilder;
use PDO;
use Symfony\Component\Uid\Uuid;
use Tulia\Cms\Menu\Domain\ReadModel\Datatable\MenuDatatableFinderInterface;
use Tulia\Component\Datatable\Finder\AbstractDatatableFinder;
use Tulia\Component\Datatable\Finder\FinderContext;
use Tulia\Component\Theme\ManagerInterface;

/**
 * @author Adam Banaszkiewicz
 */
class DbalMenuDatatableFinder extends AbstractDatatableFinder implements MenuDatatableFinderInterface
{
    public function __construct(
        private readonly ManagerInterface $manager,
        Connection $connection,
    ) {
        parent::__construct($connection);
    }

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
            'spaces' => [
                'selector' => 'tm.spaces',
                'label' => 'menuSpace',
                'translation_domain' => 'menu',
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

    public function prepareResult(array $result): array
    {
        $spaces = $this->manager->getTheme()->getConfig()->getMenuSpaces();

        foreach ($result as $key => $value) {
            $tmp = explode(',', (string) $value['spaces']);

            foreach ($tmp as $k => $v) {
                if (isset($spaces[$v])) {
                    $tmp[$k] = $spaces[$v]['label'];
                }
            }

            $result[$key]['spaces_raw'] = $result[$key]['spaces'];
            $result[$key]['spaces'] = implode(', ', $tmp);
        }

        return $result;
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
