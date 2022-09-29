<?php

declare(strict_types=1);

namespace Tulia\Cms\Menu\Infrastructure\Persistence\Domain\ReadModel\Datatable;

use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Query\QueryBuilder;
use PDO;
use Symfony\Component\Uid\Uuid;
use Symfony\Contracts\Translation\TranslatorInterface;
use Tulia\Cms\Menu\Domain\ReadModel\Datatable\ItemDatatableFinderInterface;
use Tulia\Component\Datatable\Finder\AbstractDatatableFinder;
use Tulia\Component\Datatable\Finder\FinderContext;

/**
 * @author Adam Banaszkiewicz
 */
final class DbalItemDatatableFinder extends AbstractDatatableFinder implements ItemDatatableFinderInterface
{
    public function __construct(
        Connection $connection,
        private readonly TranslatorInterface $translator,
    ) {
        parent::__construct($connection);
    }

    public function getColumns(FinderContext $context): array
    {
        return [
            'id' => [
                'selector' => 'BIN_TO_UUID(ti.id)',
                'type' => 'uuid',
                'label' => 'ID',
            ],
            'name' => [
                'selector' => 'tl.name',
                'label' => 'name',
                'view' => '@backend/menu/item/parts/datatable/name.tpl',
            ],
            'visibility' => [
                'selector' => 'tl.visibility',
                'label' => 'visibility',
                'html_attr' => ['class' => 'text-center'],
                'value_translation' => [
                    '1' => $this->translator->trans('visible'),
                    '0' => $this->translator->trans('invisible'),
                ],
                'value_class' => [
                    '1' => 'text-success',
                    '0' => 'text-danger',
                ],
            ],
        ];
    }

    public function prepareQueryBuilder(QueryBuilder $queryBuilder, FinderContext $context): QueryBuilder
    {
        $queryBuilder
            ->from('#__menu_item', 'ti')
            ->innerJoin('ti', '#__menu', 'tm', 'tm.id = ti.menu_id')
            ->leftJoin('ti', '#__menu_item_translation', 'tl', 'ti.id = tl.item_id AND tl.locale = :locale')
            ->addSelect('BIN_TO_UUID(tm.id) AS menu_id, ti.level, BIN_TO_UUID(ti.parent_id) AS parent_id, ti.is_root, tl.translated')
            ->where('tm.id = :menu_id')
            ->andWhere('tm.website_id = :website_id')
            ->setParameter('menu_id', Uuid::fromString($context['menu_id'])->toBinary(), PDO::PARAM_STR)
            ->setParameter('website_id', Uuid::fromString($context['website']->getId())->toBinary(), PDO::PARAM_STR)
            ->setParameter('locale', $context['website']->getLocale()->getCode(), PDO::PARAM_STR)
            ->addOrderBy('ti.level', 'ASC')
            ->addOrderBy('ti.position', 'ASC')
        ;

        return $queryBuilder;
    }

    public function prepareResult(array $result): array
    {
        if ($result === []) {
            return $result;
        }

        $root = null;

        foreach ($result as $row) {
            if ($row['is_root']) {
                $root = $row['id'];
            }
        }

        return $this->sort($result, $root);
    }

    public function buildActions(FinderContext $context, array $row): array
    {
        return [
            'main' => '@backend/menu/item/parts/datatable/links/edit-link.tpl',
            'delete' => '@backend/menu/item/parts/datatable/links/delete-link.tpl',
        ];
    }

    private function sort(array $items, ?string $parent, int $level = 1): array
    {
        $result = [];

        foreach ($items as $item) {
            $item['level'] = (int) $item['level'];

            if ($item['level'] === $level && $item['parent_id'] === $parent) {
                $result[] = [$item];
                $result[] = $this->sort($items, $item['id'], $level + 1);
            }
        }

        if ($result === []) {
            return [];
        }

        return array_merge(...$result);
    }
}
