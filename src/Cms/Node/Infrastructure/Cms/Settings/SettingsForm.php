<?php

declare(strict_types=1);

namespace Tulia\Cms\Node\Infrastructure\Cms\Settings;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Contracts\Translation\TranslatorInterface;
use Tulia\Cms\Content\Type\Domain\ReadModel\Service\ContentTypeRegistryInterface;

/**
 * @author Adam Banaszkiewicz
 */
final class SettingsForm extends AbstractType
{
    public function __construct(
        private readonly ContentTypeRegistryInterface $contentTypeRegistry,
        private readonly TranslatorInterface $translator,
    ) {
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $choices = [$this->translator->trans('noCategoryForThisNodeType', [], 'node') => ''];

        foreach ($this->contentTypeRegistry->allByType('node') as $contentType) {
            $choices[$contentType->getName()] = $contentType->getCode();
        }

        $builder
            ->add('category_taxonomy', Type\ChoiceType::class, [
                'label' => 'categoryTaxonomy',
                'translation_domain' => 'node',
                'choices' => $choices,
                'choice_translation_domain' => false,
                'constraints' => [
                    new Assert\NotBlank(),
                    new Assert\Choice(['choices' => $choices]),
                ],
            ]);
    }
}
