<?php

declare(strict_types=1);

namespace Tulia\Cms\Menu\UserInterface\Web\Backend\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints\Choice;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Contracts\Translation\TranslatorInterface;
use Tulia\Cms\Menu\Domain\Builder\Type\RegistryInterface;
use Tulia\Cms\Menu\Domain\Builder\Type\TypeInterface;
use Tulia\Cms\Platform\Infrastructure\Framework\Form\FormType\YesNoType;

/**
 * @author Adam Banaszkiewicz
 */
final class MenuItemDetailsForm extends AbstractType
{
    public function __construct(
        protected RegistryInterface $registry,
        protected TranslatorInterface $translator
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
        $builder->add('visibility', YesNoType::class);
        $builder->add('type', ChoiceType::class, [
            'choices' => $itemTypes,
            'choice_translation_domain' => false,
            'constraints' => [
                new NotBlank(),
                new Choice([ 'choices' => $itemTypes ]),
            ]
        ]);
        $builder->add('hash', TextType::class);
        $builder->add('identity', HiddenType::class);
        $builder->add('target', ChoiceType::class, [
            'choices' => $itemTargets,
            'choice_translation_domain' => false,
            'constraints' => [
                new Choice([ 'choices' => $itemTargets ])
            ],
        ]);
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
