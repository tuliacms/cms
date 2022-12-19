<?php

declare(strict_types=1);

namespace Tulia\Cms\Taxonomy\UserInterface\Web\Backend\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints\NotBlank;

/**
 * @author Adam Banaszkiewicz
 */
final class TaxonomyTermDetailsForm extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder->add('id', HiddenType::class);
        $builder->add('name', TextType::class, [
            'label' => 'name',
            'constraints' => [
                new NotBlank(),
            ]
        ]);
        $builder->add('slug', TextType::class, [
            'label' => 'slug'
        ]);
    }
}
