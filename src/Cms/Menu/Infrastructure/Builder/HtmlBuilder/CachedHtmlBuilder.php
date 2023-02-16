<?php

declare(strict_types=1);

namespace Tulia\Cms\Menu\Infrastructure\Builder\HtmlBuilder;

use Symfony\Contracts\Cache\ItemInterface;
use Symfony\Contracts\Cache\TagAwareCacheInterface;
use Tulia\Cms\Menu\Domain\Builder\Hierarchy\HierarchyInterface;
use Tulia\Cms\Menu\Domain\Builder\HtmlBuilder\HtmlBuilderInterface;

/**
 * @author Adam Banaszkiewicz
 */
class CachedHtmlBuilder implements HtmlBuilderInterface
{
    public function __construct(
        private readonly HtmlBuilderInterface $builder,
        private readonly TagAwareCacheInterface $cacheMenu,
    ) {
    }

    public function buildUsingHierarchy(HierarchyInterface $hierarchy, string $layout): string
    {
        return $this->cacheMenu->get('menu_html_'.$layout.'_'.$hierarchy->getCacheKey(), function (ItemInterface $item) use ($hierarchy, $layout) {
            $item->tag('menu');
            $item->tag('menu_html');
            $item->tag(sprintf('menu_%s', $hierarchy->getId()));

            return $this->builder->buildUsingHierarchy($hierarchy, $layout);
        });
    }
}
