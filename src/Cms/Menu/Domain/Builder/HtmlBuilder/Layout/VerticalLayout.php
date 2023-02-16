<?php

declare(strict_types=1);

namespace Tulia\Cms\Menu\Domain\Builder\HtmlBuilder\Layout;

use Tulia\Cms\Menu\Domain\Builder\Hierarchy\HierarchyInterface;

/**
 * @author Adam Banaszkiewicz
 */
final class VerticalLayout extends AbstractLayout
{
    public function build(HierarchyInterface $hierarchy): string
    {
        $result = '<ul class="navbar-nav tulia-navbar tulia-navbar__vertical">';

        foreach ($hierarchy as $item) {
            if ($item->isRoot()) {
                $result .= $this->buildItem($item);
            }
        }

        return $result . '</ul>';
    }
}
