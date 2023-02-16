<?php

declare(strict_types=1);

namespace Tulia\Cms\Menu\Domain\Builder\Hierarchy;

use Tulia\Cms\Menu\Domain\Builder\Criteria;
use Tulia\Cms\Menu\Domain\Builder\Hierarchy\Item as BuilderItem;
use Tulia\Cms\Menu\Domain\Builder\Identity\Identity;
use Tulia\Cms\Menu\Domain\Builder\Identity\RegistryInterface;
use Tulia\Cms\Menu\Domain\ReadModel\Finder\MenuFinderInterface;
use Tulia\Cms\Menu\Domain\ReadModel\Finder\MenuFinderScopeEnum;
use Tulia\Cms\Menu\Domain\ReadModel\Model\Item;
use Tulia\Cms\Menu\Domain\ReadModel\Model\Menu;

/**
 * @author Adam Banaszkiewicz
 */
class HierarchyBuilder implements HierarchyBuilderInterface
{
    public function __construct(
        private readonly MenuFinderInterface $menuFinder,
        private readonly RegistryInterface $registry,
    ) {
    }

    public function build(Criteria $criteria): HierarchyInterface
    {
        $menu = $this->getMenu($criteria);

        if (!$menu) {
            return new Hierarchy('', '');
        }

        $hierarchy = new Hierarchy($menu->getId(), Hierarchy::cacheKeyFromCriteria($criteria));
        $items = $menu->getItems();

        foreach ($items as $item) {
            if ($item->getLevel() === 1) {
                $hierarchy->append($this->buildFor($item, $items, $criteria->locale));
            }
        }

        return $hierarchy;
    }

    private function getMenu(Criteria $criteria): ?Menu
    {
        return $this->menuFinder->findOne([
            'website_id' => $criteria->websiteId,
            'locale' => $criteria->locale,
            'fetch_root' => true,
            $criteria->by => $criteria->value,
        ], MenuFinderScopeEnum::BUILD_MENU);
    }

    private function buildFor(Item $sourceItem, array $collection, string $locale): BuilderItem
    {
        $identity = $this->registry->provide($sourceItem->getType(), $sourceItem->getIdentity(), $locale);

        $item = new BuilderItem();
        $item->setId($sourceItem->getId());
        $item->setLevel($sourceItem->getLevel());
        $item->setLabel($sourceItem->getName());
        $item->setTarget($sourceItem->getTarget());
        $item->setHash($sourceItem->getHash());
        $item->setIdentity($identity ?: new Identity(''));

        /** @var Item $cItem */
        foreach ($collection as $cItem) {
            if ($cItem->getParentId() === $sourceItem->getId()) {
                $item->addChild($this->buildFor($cItem, $collection, $locale));
            }
        }

        return $item;
    }
}
