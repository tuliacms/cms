<?php

declare(strict_types=1);

namespace Tulia\Cms\Content\Type\Infrastructure\Framework\Form\FormType;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Tulia\Cms\Content\Type\Domain\ReadModel\Model\ContentType;
use Tulia\Cms\Content\Type\Infrastructure\Framework\Form\Service\SymfonyFieldBuilder;
use Tulia\Cms\Platform\Infrastructure\Framework\Routing\Website\WebsiteInterface;

/**
 * @author Adam Banaszkiewicz
 */
class RepeatableGroupType extends AbstractType
{
    public function __construct(
        private readonly SymfonyFieldBuilder $symfonyFieldBuilder,
    ) {
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        foreach ($options['fields'] as $field) {
            $this->symfonyFieldBuilder->buildFieldAndAddToBuilder(
                $builder,
                $options['content_type'],
                $field,
                $options['website'],
            );
        }

        $builder->add('__order', HiddenType::class, [
            'attr' => [
                'class' => 'repeatable-element-order-store',
            ],
        ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefault('repeatable_field', true);

        $resolver->setRequired('content_type');
        $resolver->addAllowedTypes('content_type', ContentType::class);

        $resolver->setRequired('fields');
        $resolver->addAllowedTypes('fields', 'array');

        $resolver->setRequired('website');
        $resolver->addAllowedTypes('website', WebsiteInterface::class);
    }

    public function buildView(FormView $view, FormInterface $form, array $options)
    {
        $view->vars['repeatable_field'] = true;
        $view->vars['content_type'] = $options['content_type'];
        $view->vars['fields'] = $options['fields'];
        $view->vars['fields_codes'] = array_map(static function ($field) {
            return $field->getCode();
        }, $options['fields']);
    }

    public function getBlockPrefix(): string
    {
        return 'content_builder_repeatable_block';
    }
}
