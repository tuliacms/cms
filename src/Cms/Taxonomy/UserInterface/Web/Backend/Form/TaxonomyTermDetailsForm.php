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

/**
 * @author Adam Banaszkiewicz
 */
final class TaxonomyTermDetailsForm extends AbstractType
{
    use AttributesAwareFormTypeTrait;

    public function __construct(
        private readonly ContentTypeRegistryInterface $contentTypeRegistry,
    ) {
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
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

        if ($this->contentTypeRegistry->get($options['content_type'])->isRoutable()) {
            $builder->add('slug', TextType::class, [
                'label' => 'slug'
            ]);
        }
    }
}
