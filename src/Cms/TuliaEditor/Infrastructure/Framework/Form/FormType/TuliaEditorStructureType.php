<?php

declare(strict_types=1);

namespace Tulia\Cms\TuliaEditor\Infrastructure\Framework\Form\FormType;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * @author Adam Banaszkiewicz
 */
class TuliaEditorStructureType extends AbstractType
{
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'label' => false
        ]);

        $resolver->setRequired('editor_field_group_id');
    }

    public function buildView(FormView $view, FormInterface $form, array $options)
    {
        $view->vars['label'] = false;
        $view->vars['editor_field_group_id'] = $options['editor_field_group_id'];
        $view->vars['attr']['data-tulia-editor-group-id'] = $options['editor_field_group_id'];
        $view->vars['attr']['class'] = 'tulia-editor-structure-field';
    }

    public function getParent(): string
    {
        return TextareaType::class;
    }

    public function getBlockPrefix(): string
    {
        return 'tulia_editor_structure';
    }
}
