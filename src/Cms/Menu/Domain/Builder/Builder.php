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
        protected HierarchyBuilderInterface $hierarchyBuilder,
        protected HtmlBuilderInterface $htmlBuilder
    ) {
    }

    /**
     * {@inheritdoc}
     */
    public function buildHierarchy(string $id, string $websiteId, string $locale): HierarchyInterface
    {
        return $this->hierarchyBuilder->build($id, $websiteId, $locale);
    }

    /**
     * {@inheritdoc}
     */
    public function buildHtml(string $id, string $websiteId, string $locale): string
    {
        return $this->htmlBuilder->build($this->buildHierarchy($id, $websiteId, $locale), $websiteId, $locale);
    }
}
