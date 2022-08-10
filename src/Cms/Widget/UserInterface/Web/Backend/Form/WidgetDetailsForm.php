<?php

declare(strict_types=1);

namespace Tulia\Cms\Widget\UserInterface\Web\Backend\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Contracts\Translation\TranslatorInterface;
use Tulia\Cms\Platform\Infrastructure\Framework\Form\FormType\YesNoType;
use Tulia\Component\Theme\ManagerInterface;

/**
 * @author Adam Banaszkiewicz
 */
final class WidgetDetailsForm extends AbstractType
{
    public function __construct(
        private ManagerInterface $themeManager,
        private TranslatorInterface $translator
    ) {
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder->add('name', TextType::class, [
            'constraints' => [
                new Assert\NotBlank()
            ]
        ]);
        $builder->add('space', ChoiceType::class, $this->buildSpaceOptions());
        $builder->add('visibility', YesNoType::class);
        $builder->add('title', TextType::class);
        $builder->add('styles', ChoiceType::class, $this->buildStylesOptions());
        $builder->add('htmlClass', TextType::class);
        $builder->add('htmlId', TextType::class);
    }

    private function buildStylesOptions(): array
    {
        $theme = $this->themeManager->getTheme();
        $widgetStyles = [];

        if ($theme->hasConfig()) {
            foreach ($theme->getConfig()->getRegisteredWidgetStyles() as $style) {
                $widgetStyles[$this->translator->trans($style['label'], [], $style['translation_domain'])] = $style['name'];
            }
        }

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
        $spaces = [];

        if ($theme->hasConfig()) {
            $spaces = $theme->getConfig()->getRegisteredWidgetSpaces();
            $spaces = array_combine(
                array_map(function ($item) {
                    return $item['label'];
                }, $spaces),
                array_map(function ($item) {
                    return $item['name'];
                }, $spaces),
            );
        }

        $options['choices'] = $spaces;
        $options['choice_translation_domain'] = false;
        $options['constraints'][] = new Assert\NotBlank();
        $options['constraints'][] = new Assert\Choice([ 'choices' => $spaces ]);

        return $options;
    }
}
