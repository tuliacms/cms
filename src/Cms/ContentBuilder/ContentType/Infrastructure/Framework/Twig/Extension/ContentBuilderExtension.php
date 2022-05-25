<?php

declare(strict_types=1);

namespace Tulia\Cms\ContentBuilder\ContentType\Infrastructure\Framework\Twig\Extension;

use Tulia\Cms\ContentBuilder\ContentType\Infrastructure\Framework\Form\ContentTypeFormDescriptor;
use Tulia\Cms\ContentBuilder\Layout\Exception\LayoutNotExists;
use Tulia\Cms\ContentBuilder\Layout\Service\LayoutBuilder;
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
                try {
                    return $this->engine->render($this->layoutBuilder->build($formDescriptor));
                } catch (LayoutNotExists $e) {
                    return sprintf('Layout "%s" defined for "%s" node type not exists. Form cannot be rendered.', $e->getLayoutName(), $e->getNodeType());
                }
            }, [
                'is_safe' => [ 'html' ],
            ]),
        ];
    }
}
