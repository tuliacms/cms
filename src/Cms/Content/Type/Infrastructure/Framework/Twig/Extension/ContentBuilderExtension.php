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
    private LayoutBuilder $layoutBuilder;
    private Engine $engine;

    public function __construct(LayoutBuilder $layoutBuilder, Engine $engine)
    {
        $this->layoutBuilder = $layoutBuilder;
        $this->engine = $engine;
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
