<?php

declare(strict_types=1);

namespace Tulia\Cms\ContentBuilder\Layout\LayoutType\Service;

use Tulia\Cms\ContentBuilder\ContentType\Domain\WriteModel\ContentType\Service\Configuration;
use Tulia\Cms\ContentBuilder\ContentType\Infrastructure\Framework\Form\ContentTypeFormDescriptor;
use Tulia\Cms\ContentBuilder\Layout\LayoutType\Exception\LayoutNotExists;
use Tulia\Component\Templating\View;

/**
 * @author Adam Banaszkiewicz
 */
class LayoutBuilder
{
    private LayoutTypeBuilderRegistry $builderRegistry;
    private Configuration $config;

    public function __construct(LayoutTypeBuilderRegistry $builderRegistry, Configuration $config)
    {
        $this->builderRegistry = $builderRegistry;
        $this->config = $config;
    }

    /**
     * @throws LayoutNotExists
     */
    public function build(ContentTypeFormDescriptor $formDescriptor): View
    {
        $type = $formDescriptor->getContentType();

        return $this->builderRegistry
            ->get($this->config->getLayoutBuilder($type->getType()))
            ->editorView($type, $formDescriptor->getFormView(), $formDescriptor->getViewContext());
    }
}
