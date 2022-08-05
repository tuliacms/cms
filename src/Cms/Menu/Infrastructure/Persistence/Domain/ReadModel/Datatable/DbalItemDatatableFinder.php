<?php

declare(strict_types=1);

namespace Tulia\Cms\Menu\Infrastructure\Persistence\Domain\ReadModel\Datatable;

use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Query\QueryBuilder;
use PDO;
use Symfony\Component\Uid\Uuid;
use Symfony\Contracts\Translation\TranslatorInterface;
use Tulia\Cms\Menu\Domain\ReadModel\Datatable\ItemDatatableFinderInterface;
use Tulia\Cms\Menu\Domain\WriteModel\Model\Item;
use Tulia\Component\Datatable\Finder\AbstractDatatableFinder;
use Tulia\Component\Datatable\Finder\FinderContext;

/**
 * @author Adam Banaszkiewicz
 */
class DbalItemDatatableFinder extends AbstractDatatableFinder implements ItemDatatableFinderInterface
{
    private TranslatorInterface $translator;

    private ?string $menuId = null;

    public function __construct(
        Connection $connection,
        TranslatorInterface $translator
    ) {
        parent::__construct($connection);

        $this->translator = $translator;
    }

    public function setMenuId(string $menuId): void
    {
        $this->menuId = $menuId;
    }

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
                'selector' => 'BIN_TO_UUID(tm.id)',
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

    /**
     * {@inheritdoc}
     */
    public function prepareQueryBuilder(QueryBuilder $queryBuilder, FinderContext $context): QueryBuilder
    {
        $queryBuilder
            ->from('#__menu_item', 'tm')
            ->leftJoin('tm', '#__menu_item_translation', 'tl', 'tm.id = tl.item_id AND tl.locale = :locale')
            ->addSelect('BIN_TO_UUID(tm.menu_id) AS menu_id, tm.level, BIN_TO_UUID(tm.parent_id) AS parent_id, tm.is_root')
            ->where('tm.menu_id = :menu_id')
            ->setParameter('menu_id', Uuid::fromString($this->menuId)->toBinary(), PDO::PARAM_STR)
            ->setParameter('locale', $context->locale, PDO::PARAM_STR)
            ->addOrderBy('tm.level', 'ASC')
            ->addOrderBy('tm.position', 'ASC')
        ;

        if (false === $context->isDefaultLocale()) {
            $queryBuilder->addSelect('IF(ISNULL(tl.title), 0, 1) AS translated');
        }

        return $queryBuilder;
    }

    /**
     * {@inheritdoc}
     */
    public function prepareResult(array $result): array
    {
        $root = null;

        foreach ($result as $row) {
            if ($row['is_root']) {
                $root = $row['id'];
            }
        }

        return $this->sort($result, parent: $root);
    }

    /**
     * {@inheritdoc}
     */
    public function buildActions(array $row): array
    {
        return [
            'main' => '@backend/menu/item/parts/datatable/links/edit-link.tpl',
            'delete' => '@backend/menu/item/parts/datatable/links/delete-link.tpl',
        ];
    }

    private function sort(array $items, int $level = 1, string $parent = Item::ROOT_ID): array
    {
        $result = [];

        foreach ($items as $item) {
            $item['level'] = (int) $item['level'];

            if ($item['level'] === $level && $item['parent_id'] === $parent) {
                $result[] = [$item];
                $result[] = $this->sort($items, $level + 1, $item['id']);
            }
        }

        if ($result === []) {
            return [];
        }

        return array_merge(...$result);
    }
}
