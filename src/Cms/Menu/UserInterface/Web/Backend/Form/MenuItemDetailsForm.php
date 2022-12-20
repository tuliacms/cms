<?php

declare(strict_types=1);

namespace Tulia\Cms\Menu\UserInterface\Web\Backend\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Choice;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Contracts\Translation\TranslatorInterface;
use Tulia\Cms\Content\Type\UserInterface\Web\Backend\Form\FormType\AttributesAwareFormTypeTrait;
use Tulia\Cms\Content\Type\UserInterface\Web\Backend\Form\FormType\AttributesType;
use Tulia\Cms\Menu\Domain\Builder\Type\RegistryInterface;
use Tulia\Cms\Menu\Domain\Builder\Type\TypeInterface;
use Tulia\Cms\Menu\UserInterface\Web\Shared\Form\FormType\MenuItemChoiceType;
use Tulia\Cms\Platform\Infrastructure\Framework\Form\FormType\YesNoType;

/**
 * @author Adam Banaszkiewicz
 */
final class MenuItemDetailsForm extends AbstractType
{
    use AttributesAwareFormTypeTrait {
        AttributesAwareFormTypeTrait::configureOptions as traitConfigureOptions;
    }

    public function __construct(
        private readonly RegistryInterface $registry,
        private readonly TranslatorInterface $translator,
    ) {
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $itemTypes = $this->buildItemTypesChoices();
        $itemTargets = $this->buildItemTargets();

        $builder->add('id', HiddenType::class);
        $builder->add('name', TextType::class, [
            'label' => 'name',
            'constraints' => [
                new NotBlank(),
            ]
        ]);
        $builder->add('visibility', YesNoType::class, [
            'label' => 'visibility',
        ]);
        $builder->add('type', ChoiceType::class, [
            'label' => 'type',
            'choices' => $itemTypes,
            'choice_translation_domain' => false,
            'constraints' => [
                new NotBlank(),
                new Choice([ 'choices' => $itemTypes ]),
            ]
        ]);
        $builder->add('hash', TextType::class, [
            'label' => 'itemHash',
            'translation_domain' => 'menu',
        ]);
        $builder->add('identity', HiddenType::class);
        $builder->add('target', ChoiceType::class, [
            'label' => 'itemTarget',
            'translation_domain' => 'menu',
            'choices' => $itemTargets,
            'choice_translation_domain' => false,
            'constraints' => [
                new Choice([ 'choices' => $itemTargets ])
            ],
        ]);
        $builder->add('parent', MenuItemChoiceType::class, [
            'label' => 'parentItem',
            'translation_domain' => 'menu',
            'menu_id' => $options['menu_id'],
            'locale' => $options['website']->getLocale()->getCode(),
            'website_id' => $options['website']->getId(),
        ]);
        $builder->add('attributes', AttributesType::class, [
            'website' => $options['website'],
            'content_type' => $options['content_type'],
            'context' => $options['context'],
        ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $this->traitConfigureOptions($resolver);

        $resolver->setDefault('menu_id', null);
        $resolver->setRequired(['menu_id']);
        $resolver->setAllowedTypes('menu_id', 'string');
    }

    private function buildItemTypesChoices(): array
    {
        $itemTypes = [];

        /** @var TypeInterface $type */
        foreach ($this->registry->all() as $type) {
            $itemTypes[$this->translator->trans($type->getLabel(), [], $type->getTranslationDomain())] = $type->getType();
        }

        return $itemTypes;
    }

    public function buildItemTargets(): array
    {
        return [
            $this->translator->trans('itemTargetAuto', [], 'menu')  => '',
            $this->translator->trans('itemTargetSelf', [], 'menu')  => '_self',
            $this->translator->trans('itemTargetBlank', [], 'menu') => '_blank',
        ];
    }
}
