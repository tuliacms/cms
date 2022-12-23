<?php

declare(strict_types=1);

namespace Tulia\Cms\Taxonomy\UserInterface\Web\Backend\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\NotBlank;
use Tulia\Cms\Content\Type\Domain\ReadModel\Service\ContentTypeRegistryInterface;
use Tulia\Cms\Content\Type\UserInterface\Web\Backend\Form\FormType\AttributesAwareFormTypeTrait;
use Tulia\Cms\Content\Type\UserInterface\Web\Backend\Form\FormType\AttributesType;
use Tulia\Cms\Taxonomy\UserInterface\Web\Shared\Form\FormType\TaxonomyTypeaheadType;

/**
 * @author Adam Banaszkiewicz
 */
final class TaxonomyTermDetailsForm extends AbstractType
{
    use AttributesAwareFormTypeTrait {
        AttributesAwareFormTypeTrait::configureOptions as traitsConfigureOptions;
    }

    public function __construct(
        private readonly ContentTypeRegistryInterface $contentTypeRegistry,
    ) {
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $contentType = $this->contentTypeRegistry->get($options['content_type']);

        $builder->add('id', HiddenType::class);
        $builder->add('name', TextType::class, [
            'label' => 'name',
            'constraints' => [
                new NotBlank(),
            ]
        ]);
        $builder->add('attributes', AttributesType::class, [
            'partial_view' => $options['partial_view'],
            'website' => $options['website'],
            'content_type' => $options['content_type'],
        ]);

        if ($contentType->isRoutable()) {
            $builder->add('slug', TextType::class, [
                'label' => 'slug'
            ]);
        }
        if ($options['form_mode'] === 'create' && $contentType->isHierarchical()) {
            $builder->add('parent', TaxonomyTypeaheadType::class, [
                'label' => 'parentTerm',
                'taxonomy_type' => $options['content_type'],
                'search_route_params' => [
                    'taxonomy_type' => $options['content_type'],
                ],
            ]);
        }
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $this->traitsConfigureOptions($resolver);

        $resolver->setRequired('form_mode');
        $resolver->setAllowedTypes('form_mode', 'string');
    }
}
