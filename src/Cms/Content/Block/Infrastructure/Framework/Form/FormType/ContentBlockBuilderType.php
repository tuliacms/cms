<?php

declare(strict_types=1);

namespace Tulia\Cms\Content\Block\Infrastructure\Framework\Form\FormType;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\Validator\Constraints\Json;
use Tulia\Cms\Content\Type\Domain\ReadModel\Service\ContentTypeRegistryInterface;

/**
 * @author Adam Banaszkiewicz
 */
class ContentBlockBuilderType extends AbstractType
{
    private ContentTypeRegistryInterface $contentTypeRegistry;
    private RouterInterface $router;
    private RequestStack $requestStack;

    public function __construct(
        ContentTypeRegistryInterface $contentTypeRegistry,
        RouterInterface $router,
        RequestStack $requestStack
    ) {
        $this->contentTypeRegistry = $contentTypeRegistry;
        $this->router = $router;
        $this->requestStack = $requestStack;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefault('compound', false);
        $resolver->setDefault('constraints', [
            new Json()
        ]);
    }

    public function buildView(FormView $view, FormInterface $form, array $options)
    {
        $types = [];

        foreach ($this->contentTypeRegistry->all() as $type) {
            if ($type->isType('content_block')) {
                $icon = null;

                foreach ($type->getFields() as $field) {
                    if ($field->isType('___content_block_icon')) {
                        $icon = $field->getConfig('icon');
                    }
                }

                $types[$type->getCode()] = [
                    'code' => $type->getCode(),
                    'name' => $type->getName(),
                    'icon' => $this->requestStack->getCurrentRequest()->getUriForPath((string) $icon),
                    'block_panel_url' => $this->router->generate('backend.content.block.panel.builder', [ 'type' => $type->getCode() ]),
                ];
            }
        }

        $view->vars['label'] = null;
        $view->vars['block_types'] = $types;
    }

    public function getBlockPrefix(): string
    {
        return 'content_block_builder';
    }
}
