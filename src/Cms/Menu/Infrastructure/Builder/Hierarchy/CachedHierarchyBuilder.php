<?php

declare(strict_types=1);

namespace Tulia\Cms\Menu\Infrastructure\Builder\Hierarchy;

use Symfony\Contracts\Cache\ItemInterface;
use Symfony\Contracts\Cache\TagAwareCacheInterface;
use Tulia\Cms\Menu\Domain\Builder\Hierarchy\HierarchyBuilderInterface;
use Tulia\Cms\Menu\Domain\Builder\Hierarchy\HierarchyInterface;

/**
 * @author Adam Banaszkiewicz
 */
class CachedHierarchyBuilder implements HierarchyBuilderInterface
{
    public function __construct(
        private HierarchyBuilderInterface $builder,
        private TagAwareCacheInterface $menuCache
    ) {
    }

    public function build(string $id, string $locale, array $collection = []): HierarchyInterface
    {
        return $this->menuCache->get(sprintf('menu_hierarchy_%s_%s', $locale, $id), function (ItemInterface $item) use ($id, $locale, $collection) {
            $item->tag('menu_hierarchy');
            $item->tag(sprintf('menu_%s', $id));

            return $this->builder->build($id, $locale, $collection);
        });
    }
}
