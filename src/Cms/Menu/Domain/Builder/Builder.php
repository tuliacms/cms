<?php

declare(strict_types=1);

namespace Tulia\Cms\Menu\Domain\Builder;

use Tulia\Cms\Menu\Domain\Builder\Hierarchy\HierarchyBuilderInterface;
use Tulia\Cms\Menu\Domain\Builder\Hierarchy\HierarchyInterface;
use Tulia\Cms\Menu\Domain\Builder\HtmlBuilder\HtmlBuilderInterface;

/**
 * @author Adam Banaszkiewicz
 */
class Builder implements BuilderInterface
{
    public function __construct(
        private readonly HierarchyBuilderInterface $hierarchyBuilder,
        private readonly HtmlBuilderInterface $htmlBuilder,
    ) {
    }

    public function buildHierarchy(Criteria $criteria): HierarchyInterface
    {
        return $this->hierarchyBuilder->build($criteria);
    }

    public function buildHtml(Criteria $criteria, string $layout): string
    {
        return $this->htmlBuilder->buildUsingHierarchy($this->buildHierarchy($criteria), $layout);
    }
}
