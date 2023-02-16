<?php

declare(strict_types=1);

namespace Tulia\Cms\Menu\Domain\Builder\Hierarchy;

use Tulia\Cms\Menu\Domain\Builder\Criteria;

/**
 * @author Adam Banaszkiewicz
 */
interface HierarchyBuilderInterface
{
    public function build(Criteria $criteria): HierarchyInterface;
}
