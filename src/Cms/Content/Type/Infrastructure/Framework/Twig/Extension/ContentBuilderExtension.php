<?php

declare(strict_types=1);

namespace Tulia\Cms\Content\Type\Infrastructure\Framework\Twig\Extension;

use Symfony\Component\Form\FormView;
use Tulia\Component\Templating\Engine;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

/**
 * @author Adam Banaszkiewicz
 */
class ContentBuilderExtension extends AbstractExtension
{
    public function __construct(
        private readonly Engine $engine,
    ) {
    }

    public function getFunctions(): array
    {
        return [
            new TwigFunction('render_content_builder_form_layout', function (FormView $form) {
                $rootForm = $form;
                $attributesForm = null;

                foreach ($form as $child) {
                    if (isset($child->vars['content_type_view'])) {
                        $attributesForm = $child;
                    }
                }

                if (!$attributesForm) {
                    throw new \LogicException('Cannot find AttributesType form in children of this form. So cannot render this form using this method.');
                }

                $attributesForm->vars['content_type_view']->addData(['form' => $rootForm]);
                $attributesForm->vars['content_type_view']->addData(['attributesForm' => $attributesForm]);

                return $this->engine->render($attributesForm->vars['content_type_view']);
            }, [
                'is_safe' => [ 'html' ],
            ]),
        ];
    }
}
