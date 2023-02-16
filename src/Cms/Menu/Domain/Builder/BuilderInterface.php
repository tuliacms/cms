<?php

declare(strict_types=1);

namespace Tulia\Cms\Menu\Domain\Builder;

use Tulia\Cms\Menu\Domain\Builder\Hierarchy\HierarchyInterface;

/**
 * @author Adam Banaszkiewicz
 */
interface BuilderInterface
{
    public function buildHierarchy(Criteria $criteria): HierarchyInterface;

    public function buildHtml(Criteria $criteria, string $layout): string;
}
