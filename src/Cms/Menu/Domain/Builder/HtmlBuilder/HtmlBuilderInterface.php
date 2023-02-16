<?php

declare(strict_types=1);

namespace Tulia\Cms\Menu\Domain\Builder\HtmlBuilder;

use Tulia\Cms\Menu\Domain\Builder\Hierarchy\HierarchyInterface;

/**
 * @author Adam Banaszkiewicz
 */
interface HtmlBuilderInterface
{
    public function buildUsingHierarchy(HierarchyInterface $hierarchy, string $layout): string;
}
