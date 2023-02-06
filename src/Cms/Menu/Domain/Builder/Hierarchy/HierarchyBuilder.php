<?php

declare(strict_types=1);

namespace Tulia\Cms\Menu\Domain\Builder\Hierarchy;

use Tulia\Cms\Menu\Domain\Builder\Hierarchy\Item as BuilderItem;
use Tulia\Cms\Menu\Domain\Builder\Identity\Identity;
use Tulia\Cms\Menu\Domain\Builder\Identity\RegistryInterface;
use Tulia\Cms\Menu\Domain\ReadModel\Finder\MenuFinderInterface;
use Tulia\Cms\Menu\Domain\ReadModel\Finder\MenuFinderScopeEnum;
use Tulia\Cms\Menu\Domain\ReadModel\Model\Item;

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

    /**
     * {@inheritdoc}
     */
    public function build(string $id, string $websiteId, string $locale, array $collection = []): HierarchyInterface
    {
        $hierarchy = new Hierarchy($id);

        $items = $collection === [] ? $this->getItems($id, $websiteId, $locale) : $collection;

        foreach ($items as $item) {
            if ($item->getLevel() === 1) {
                $hierarchy->append($this->buildFor($item, $items, $locale));
            }
        }

        return $hierarchy;
    }

    private function getItems(string $id, string $websiteId, string $locale): array
    {
        $menu = $this->menuFinder->findOne([
            'id' => $id,
            'website_id' => $websiteId,
            'locale' => $locale,
            'fetch_root' => true,
        ], MenuFinderScopeEnum::BUILD_MENU);

        return $menu ? $menu->getItems() : [];
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
