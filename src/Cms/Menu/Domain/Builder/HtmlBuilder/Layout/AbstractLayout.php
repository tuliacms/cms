<?php

declare(strict_types=1);

namespace Tulia\Cms\Menu\Domain\Builder\HtmlBuilder\Layout;

use Tulia\Cms\Menu\Domain\Builder\Hierarchy\HierarchyInterface;
use Tulia\Cms\Menu\Domain\Builder\Hierarchy\Item;

/**
 * @author Adam Banaszkiewicz
 */
abstract class AbstractLayout implements MenuLayoutInterface
{
    abstract public function build(HierarchyInterface $hierarchy): string;

    protected function buildAttributes(array $attributes): string
    {
        $result = [];

        foreach ($attributes as $key => $val) {
            $result[] = $key . '="' . $val . '"';
        }

        return ' ' . implode(' ', $result);
    }

    protected function buildItem(Item $item): string
    {
        $children = '';

        if ($item->hasChildren()) {
            $children = '<ul class="submenu submenu-level-' . ($item->getLevel() + 1) . '">';

            foreach ($item->getChildren() as $child) {
                $children .= $this->buildItem($child);
            }

            $children .= '</ul>';
        }

        $liAttributes = [];
        $liAttributes['class'] = 'nav-item';

        if ($children) {
            $liAttributes['class'] .= ' has-children';
        }

        $aAttributes = [];
        $aAttributes['class'] = 'nav-link';
        $aAttributes['href']  = $item->getLink();
        $aAttributes['title'] = $item->getLabel();

        if ($item->getTarget()) {
            $aAttributes['target'] = $item->getTarget();
        }

        $result = '<li' . $this->buildAttributes($liAttributes) . '>';

        $result .= '<a' . $this->buildAttributes($aAttributes) . '>' . $item->getLabel() . '</a>';
        $result .= $children;

        return $result . '</li>';
    }
}
