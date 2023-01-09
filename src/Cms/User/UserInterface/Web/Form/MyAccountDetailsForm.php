<?php

declare(strict_types=1);

namespace Tulia\Cms\User\UserInterface\Web\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints as Assert;
use Tulia\Cms\Content\Type\UserInterface\Web\Backend\Form\FormType\AttributesAwareFormTypeTrait;
use Tulia\Cms\Content\Type\UserInterface\Web\Backend\Form\FormType\AttributesType;
use Tulia\Cms\Platform\Infrastructure\Framework\Form\FormType\LocaleType;
use Tulia\Cms\User\Infrastructure\Framework\Form\FormType\UserAvatar\UserAvatarModelTransformer;
use Tulia\Cms\User\Infrastructure\Framework\Form\FormType\UserAvatar\UserAvatarType;

/**
 * @author Adam Banaszkiewicz
 */
final class MyAccountDetailsForm extends AbstractType
{
    use AttributesAwareFormTypeTrait;

    public function __construct(
        private readonly UserAvatarModelTransformer $userAvatarTransformer,
    ) {
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder->add('locale', LocaleType::class, [
            'constraints' => [
                new Assert\NotBlank(),
            ]
        ]);
        $builder->add('name', TextType::class, [
            'label' => 'User name (first/last name)'
        ]);
        $builder->add('avatar', UserAvatarType::class, [
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
        $builder->get('avatar')->addModelTransformer($this->userAvatarTransformer);
        $builder->add('remove_avatar', CheckboxType::class, [
            'label' => 'Remove avatar'
        ]);
        $builder->add('attributes', AttributesType::class, [
            'website' => $options['website'],
            'content_type' => $options['content_type'],
        ]);
    }
}
