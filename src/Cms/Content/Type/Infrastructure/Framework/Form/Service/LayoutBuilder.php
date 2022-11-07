<?php

declare(strict_types=1);

namespace Tulia\Cms\Content\Type\Infrastructure\Framework\Form\Service;

use Tulia\Cms\Content\Type\Domain\WriteModel\Service\Configuration;
use Tulia\Cms\Content\Type\Infrastructure\Framework\Form\ContentTypeFormDescriptor;
use Tulia\Component\Templating\View;

/**
 * @author Adam Banaszkiewicz
 */
class LayoutBuilder
{
    public function __construct(
        private readonly LayoutTypeBuilderRegistry $builderRegistry,
        private readonly Configuration $config,
    ) {
    }

    public function build(ContentTypeFormDescriptor $formDescriptor): View
    {
        $type = $formDescriptor->getContentType();

        return $this->builderRegistry
            ->get($this->config->getLayoutBuilder($type->getType()))
            ->editorView($type, $formDescriptor->getFormView(), $formDescriptor->getViewContext());
    }
}
