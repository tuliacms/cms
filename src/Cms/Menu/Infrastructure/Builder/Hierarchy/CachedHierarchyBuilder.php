<?php

declare(strict_types=1);

namespace Tulia\Cms\Menu\Infrastructure\Builder\Hierarchy;

use Symfony\Contracts\Cache\ItemInterface;
use Symfony\Contracts\Cache\TagAwareCacheInterface;
use Tulia\Cms\Menu\Domain\Builder\Criteria;
use Tulia\Cms\Menu\Domain\Builder\Hierarchy\Hierarchy;
use Tulia\Cms\Menu\Domain\Builder\Hierarchy\HierarchyBuilderInterface;
use Tulia\Cms\Menu\Domain\Builder\Hierarchy\HierarchyInterface;

/**
 * @author Adam Banaszkiewicz
 */
class CachedHierarchyBuilder implements HierarchyBuilderInterface
{
    public function __construct(
        private readonly HierarchyBuilderInterface $builder,
        private readonly TagAwareCacheInterface $cacheMenu,
    ) {
    }

    public function build(Criteria $criteria): HierarchyInterface
    {
        $key = Hierarchy::cacheKeyFromCriteria($criteria);

        return $this->cacheMenu->get($key, function (ItemInterface $item) use ($criteria) {
            $hierarchy = $this->builder->build($criteria);

            $item->tag('menu');
            $item->tag('menu_hierarchy');
            $item->tag(sprintf('menu_%s', $hierarchy->getId()));

            return $hierarchy;
        });
    }
}
