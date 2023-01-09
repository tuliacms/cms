<?php

declare(strict_types=1);

namespace Tulia\Cms\Widget\UserInterface\Web\Backend\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Contracts\Translation\TranslatorInterface;
use Tulia\Cms\Content\Type\UserInterface\Web\Backend\Form\FormType\AttributesAwareFormTypeTrait;
use Tulia\Cms\Content\Type\UserInterface\Web\Backend\Form\FormType\AttributesType;
use Tulia\Cms\Platform\Infrastructure\Framework\Form\FormType\YesNoType;
use Tulia\Component\Theme\ManagerInterface;

/**
 * @author Adam Banaszkiewicz
 */
final class WidgetDetailsForm extends AbstractType
{
    use AttributesAwareFormTypeTrait {
        AttributesAwareFormTypeTrait::configureOptions as traitConfigureOptions;
    }

    public function __construct(
        private readonly ManagerInterface $themeManager,
        private readonly TranslatorInterface $translator
    ) {
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder->add('name', TextType::class, [
            'label' => 'name',
            'constraints' => [
                new Assert\NotBlank()
            ]
        ]);
        $builder->add('space', ChoiceType::class, $this->buildSpaceOptions());
        $builder->add('visibility', YesNoType::class, [
            'label' => 'visibility',
        ]);
        $builder->add('title', TextType::class, [
            'label' => 'title',
        ]);
        $builder->add('styles', ChoiceType::class, $this->buildStylesOptions());
        $builder->add('htmlClass', TextType::class, [
            'label' => 'htmlClass',
            'translation_domain' => 'widgets',
        ]);
        $builder->add('htmlId', TextType::class, [
            'label' => 'htmlId',
            'translation_domain' => 'widgets',
        ]);
        $builder->add('attributes', AttributesType::class, [
            'partial_view' => $options['partial_view'],
            'website' => $options['website'],
            'content_type' => $options['content_type'],
        ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $this->traitConfigureOptions($resolver);

        $resolver->setDefault('attr', ['class' => 'tulia-dynamic-form']);
    }

    private function buildStylesOptions(): array
    {
        $theme = $this->themeManager->getTheme();
        $widgetStyles = [];

        foreach ($theme->getConfig()->getWidgetStyles() as $style) {
            $widgetStyles[$this->translator->trans($style['label'], [], $theme->getConfig()->getTranslationDomain())] = $style['name'];
        }

        $options['label'] = 'styles';
        $options['translation_domain'] = 'widgets';
        $options['multiple'] = true;
        $options['choices'] = $widgetStyles;
        $options['choice_translation_domain'] = false;
        $options['translation_domain'] = 'widgets';
        $options['help'] = 'stylesDescription';
        $options['constraints'][] = new Assert\Choice([ 'choices' => $widgetStyles, 'multiple' => true ]);

        return $options;
    }

    public function buildSpaceOptions(): array
    {
        $theme = $this->themeManager->getTheme();

        $spaces = $theme->getConfig()->getWidgetSpaces();
        $spaces = array_combine(
            array_map(function ($item) {
                return $item['label'];
            }, $spaces),
            array_map(function ($item) {
                return $item['name'];
            }, $spaces),
        );

        $options['label'] = 'space';
        $options['translation_domain'] = 'widgets';
        $options['choices'] = $spaces;
        $options['choice_translation_domain'] = false;
        $options['constraints'][] = new Assert\NotBlank();
        $options['constraints'][] = new Assert\Choice([ 'choices' => $spaces ]);

        return $options;
    }
}
