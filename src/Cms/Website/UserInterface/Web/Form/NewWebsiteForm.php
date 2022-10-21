<?php

declare(strict_types=1);

namespace Tulia\Cms\Website\UserInterface\Web\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\Extension\Core\Type;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\Context\ExecutionContextInterface;
use Tulia\Cms\Platform\Infrastructure\Framework\Routing\Website\SslModeEnum;
use Tulia\Cms\Website\UserInterface\Web\Form\FormType\LocaleChoiceType;

/**
 * @author Adam Banaszkiewicz
 */
final class NewWebsiteForm extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('name', Type\TextType::class, [
                'required' => true,
                'constraints' => [
                    new Assert\NotBlank(),
                ],
            ])
            ->add('activity', Type\ChoiceType::class, [
                'required' => true,
                'choices' => [
                    'Yes' => 1,
                    'No' => 0,
                ],
                'constraints' => [
                    new Assert\Range(['min' => 0, 'max' => 1]),
                ],
            ])
            ->add('domain', Type\TextType::class, [
                'required' => true,
                'constraints' => [
                    new Assert\NotBlank(),
                    new Assert\Callback(function ($object, ExecutionContextInterface $context) {
                        if ($object && filter_var($object, FILTER_VALIDATE_DOMAIN, FILTER_FLAG_HOSTNAME) === false) {
                            $context->buildViolation('domainIsInvalid')
                                ->setTranslationDomain('websites')
                                ->atPath('domain')
                                ->addViolation();
                        }
                    }),
                ],
            ])
            ->add('domainDevelopment', Type\TextType::class, [
                'required' => true,
                'constraints' => [
                    new Assert\NotBlank(),
                    new Assert\Callback(function ($object, ExecutionContextInterface $context) {
                        if ($object && filter_var($object, FILTER_VALIDATE_DOMAIN, FILTER_FLAG_HOSTNAME) === false) {
                            $context->buildViolation('domainIsInvalid')
                                ->setTranslationDomain('websites')
                                ->atPath('domain_development')
                                ->addViolation();
                        }
                    }),
                ],
            ])
            ->add('pathPrefix', Type\TextType::class, [
                'constraints' => [
                    new Assert\Regex([
                        'pattern' => '/^\/{1}[a-z0-9-_]+$/',
                        'message' => 'websites.pleaseProvideValidPathWithPrecededSlash'
                    ]),
                ],
            ])
            ->add('locale', LocaleChoiceType::class, [
                'multiple' => false,
                'required' => true,
                'constraints' => [
                    new Assert\NotBlank(),
                ],
            ])
            ->add('backendPrefix', Type\TextType::class, [
                'required' => true,
                'constraints' => [
                    new Assert\NotBlank(),
                    new Assert\Regex([
                        'pattern' => '/^\/{1}[a-z0-9-_]+$/',
                        'message' => 'websites.pleaseProvideValidPathWithPrecededSlash'
                    ]),
                ],
            ])
            ->add('sslMode', Type\ChoiceType::class, [
                'constraints' => [
                    new Assert\Choice(['choices' =>[
                        'ALLOWED_BOTH'  => SslModeEnum::ALLOWED_BOTH->value,
                        'FORCE_NON_SSL' => SslModeEnum::FORCE_NON_SSL->value,
                        'FORCE_SSL'     => SslModeEnum::FORCE_SSL->value,
                    ]]),
                ],
                'choices' => [
                    'ALLOWED_BOTH'  => SslModeEnum::ALLOWED_BOTH->value,
                    'FORCE_NON_SSL' => SslModeEnum::FORCE_NON_SSL->value,
                    'FORCE_SSL'     => SslModeEnum::FORCE_SSL->value,
                ],
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefault('csrf_protection', false);
    }
}
