<?php

declare(strict_types=1);

namespace Tulia\Cms\User\UserInterface\Web\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints as Assert;
use Tulia\Cms\Content\Type\UserInterface\Web\Backend\Form\FormType\AttributesAwareFormTypeTrait;
use Tulia\Cms\Content\Type\UserInterface\Web\Backend\Form\FormType\AttributesType;
use Tulia\Cms\Platform\Infrastructure\Framework\Form\FormType\LocaleType;
use Tulia\Cms\Platform\Infrastructure\Framework\Form\FormType\PasswordType;
use Tulia\Cms\Platform\Infrastructure\Framework\Form\FormType\YesNoType;
use Tulia\Cms\User\Infrastructure\Framework\Form\FormType\UserAvatar\UserAvatarModelTransformer;
use Tulia\Cms\User\Infrastructure\Framework\Form\FormType\UserAvatar\UserAvatarType;
use Tulia\Cms\User\Infrastructure\Framework\Form\FormType\UserRoles\UserRolesType;
use Tulia\Cms\User\Infrastructure\Framework\Validator\Constraints\EmailUnique;

/**
 * @author Adam Banaszkiewicz
 */
final class UserDetailsForm extends AbstractType
{
    use AttributesAwareFormTypeTrait {
        AttributesAwareFormTypeTrait::configureOptions as traitConfigureOptions;
    }

    public function __construct(
        private readonly UserAvatarModelTransformer $userAvatarTransformer,
    ) {
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder->add('id', HiddenType::class);
        $builder->add('email', TextType::class, [
            'label' => 'email',
            'constraints' => [
                new Assert\NotBlank(),
                new Assert\Email(),
                new EmailUnique(),
            ]
        ]);
        $builder->add('locale', LocaleType::class, [
            'label' => 'language',
            'constraints' => [
                new Assert\NotBlank(),
            ]
        ]);
        $builder->add('name', TextType::class, [
            'label' => 'userFirstAndLastName',
            'translation_domain' => 'users',
        ]);
        $builder->add('enabled', YesNoType::class, [
            'label' => 'enabled',
        ]);
        $builder->add('roles', UserRolesType::class, [
            'label' => 'userRoles',
            'translation_domain' => 'users',
            'constraints' => [
                new Assert\NotBlank(),
            ]
        ]);
        $builder->add('avatar', UserAvatarType::class, [
            'label' => false,
            'constraints' => [
                new Assert\Image([
                    'minWidth' => 100,
                    'minHeight' => 100,
                    'maxWidth' => 700,
                    'maxHeight' => 700,
                    'allowLandscape' => false,
                    'allowPortrait' => false,
                    'mimeTypes' => ['image/jpg', 'image/jpeg', 'image/png', 'image/gif'],
                ])
            ],
        ]);
        $builder->add('attributes', AttributesType::class, [
            'partial_view' => $options['partial_view'],
            'website' => $options['website'],
            'content_type' => $options['content_type'],
            'context' => $options['context'],
        ]);
        $builder->get('avatar')->addModelTransformer($this->userAvatarTransformer);
        $builder->add('remove_avatar', CheckboxType::class, [
            'label' => 'removeAvatar',
            'translation_domain' => 'users',
        ]);


        $passwordField = [
            'type' => PasswordType::class,
            'invalid_message' => 'The password fields must match.',
            'first_options' => ['label' => 'password'],
            'second_options' => ['label' => 'passwordRepeat'],
        ];

        if (!$options['edit_form']) {
            $passwordField['required'] = true;
            $passwordField['constraints'] = [
                new Assert\NotBlank(),
            ];
        }

        $builder->add('password', RepeatedType::class, $passwordField);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $this->traitConfigureOptions($resolver);

        $resolver->setDefault('edit_form', false);
    }
}
