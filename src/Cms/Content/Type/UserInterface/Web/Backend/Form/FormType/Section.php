<?php

declare(strict_types=1);

namespace Tulia\Cms\Content\Type\UserInterface\Web\Backend\Form\FormType;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Callback;
use Symfony\Component\Validator\Constraints\NotBlank;
use Tulia\Cms\Content\Type\Infrastructure\Framework\Form\Validator\CodenameValidator;

/**
 * @author Adam Banaszkiewicz
 */
class Section extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('code', TextType::class, [
                'constraints' => [
                    new NotBlank(),
                    new Callback([new CodenameValidator(), 'validateSectionCode']),
                ],
            ])
            ->add('name', TextType::class)
            ->add('active', TextType::class/*, [
                'constraints' => [
                    new NotBlank(),
                ],
            ]*/)
            ->add('fields', CollectionType::class, [
                'entry_type' => Field::class,
                'allow_add' => true,
                'allow_delete' => true,
                'entry_options' => [
                    'max_depth_fields' => $options['max_depth_fields'],
                ],
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setRequired('max_depth_fields');
    }
}
