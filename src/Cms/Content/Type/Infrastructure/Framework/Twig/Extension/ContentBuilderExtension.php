<?php

declare(strict_types=1);

namespace Tulia\Cms\Content\Type\Infrastructure\Framework\Twig\Extension;

use Tulia\Cms\Content\Type\Infrastructure\Framework\Form\ContentTypeFormDescriptor;
use Tulia\Cms\Content\Type\Infrastructure\Framework\Form\Service\LayoutBuilder;
use Tulia\Component\Templating\Engine;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

/**
 * @author Adam Banaszkiewicz
 */
class ContentBuilderExtension extends AbstractExtension
{
    public function __construct(
        private readonly LayoutBuilder $layoutBuilder,
        private readonly Engine $engine,
    ) {
    }

    public function getFunctions(): array
    {
        return [
            new TwigFunction('render_content_builder_form_layout', function (ContentTypeFormDescriptor $formDescriptor) {
                return $this->engine->render($this->layoutBuilder->build($formDescriptor));
            }, [
                'is_safe' => [ 'html' ],
            ]),
        ];
    }
}
