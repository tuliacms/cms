<?php

declare(strict_types=1);

namespace Tulia\Cms\Menu\Domain\Builder\HtmlBuilder;

use Tulia\Cms\Menu\Domain\Builder\Hierarchy\HierarchyInterface;
use Tulia\Cms\Menu\Domain\Builder\HtmlBuilder\Layout\HorizontalLayout;
use Tulia\Cms\Menu\Domain\Builder\HtmlBuilder\Layout\VerticalLayout;

/**
 * @author Adam Banaszkiewicz
 */
class HtmlBuilder implements HtmlBuilderInterface
{
    public function buildUsingHierarchy(HierarchyInterface $hierarchy, string $layout): string
    {
        return match ($layout) {
            'vertical' => (new VerticalLayout())->build($hierarchy),
            default => (new HorizontalLayout())->build($hierarchy),
        };
    }
}
