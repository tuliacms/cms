<?php

declare(strict_types=1);

namespace Tulia\Cms\Content\Type\UserInterface\Web\Backend\Form\FormType;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Tulia\Cms\Content\Type\Domain\ReadModel\Service\ContentTypeRegistryInterface;
use Tulia\Cms\Content\Type\Domain\WriteModel\Service\Configuration;
use Tulia\Cms\Content\Type\Infrastructure\Framework\Form\Service\AttributesDataFlattener;
use Tulia\Cms\Content\Type\Infrastructure\Framework\Form\Service\ContentFormService;
use Tulia\Cms\Content\Type\Infrastructure\Framework\Form\Service\LayoutTypeBuilderRegistry;
use Tulia\Cms\Platform\Infrastructure\Framework\Routing\Website\WebsiteInterface;

/**
 * @author Adam Banaszkiewicz
 */
final class AttributesType extends AbstractType
{
    public function __construct(
        private readonly ContentTypeRegistryInterface $contentTypeRegistry,
        private readonly ContentFormService $contentFormService,
        private readonly LayoutTypeBuilderRegistry $builderRegistry,
        private readonly Configuration $config,
        private readonly AttributesDataFlattener $attributesDataFlattener,
    ) {
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $this->contentFormService->buildUsingBuilder(
            $builder,
            $options['website'],
            $options['content_type'],
            []
        );

        $builder->addEventListener(FormEvents::PRE_SET_DATA, function (FormEvent $event) use ($options) {
            if (!$event->getData()) {
                return;
            }

            $event->setData(
                $this->attributesDataFlattener->flattenAttributes(
                    $event->getData(),
                    $this->contentTypeRegistry->get($options['content_type'])
                )
            );
        });
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefault('label', false);
        $resolver->setDefault('__this_is_content_type_attributes_form', true);

        $resolver->setRequired('website');
        $resolver->setAllowedTypes('website', WebsiteInterface::class);

        $resolver->setRequired('content_type');
        $resolver->setAllowedTypes('content_type', 'string');

        $resolver->setDefault('partial_view', null);
        $resolver->setAllowedTypes('partial_view', ['null', 'string']);

        $resolver->setDefault('context', null);
        $resolver->setAllowedTypes('context', ['null', 'array']);
    }

    public function buildView(FormView $view, FormInterface $form, array $options): void
    {
        $type = $this->contentTypeRegistry->get($options['content_type']);
        $context = [
            'partial_view' => $options['partial_view'],
        ];

        if (is_array($options['context'])) {
            $context += $options['context'];
        }

        $view->vars['content_type_view'] = $this->builderRegistry
            ->get($this->config->getLayoutBuilder($type->getType()))
            ->editorView($type, $view, $context);

        $view->vars['content_type'] = $type;
    }
}
