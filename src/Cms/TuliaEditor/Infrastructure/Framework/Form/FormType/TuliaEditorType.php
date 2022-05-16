<?php

declare(strict_types=1);

namespace Tulia\Cms\TuliaEditor\Infrastructure\Framework\Form\FormType;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * @author Adam Banaszkiewicz
 */
class TuliaEditorType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $groupId = uniqid('tulia-editor-group-');

        $builder->add('tulia_editor_instance', TuliaEditorInstanceType::class, [
            'editor_field_group_id' => $groupId,
        ]);
        $builder->add('tulia_editor_structure', TuliaEditorStructureType::class, [
            'editor_field_group_id' => $groupId,
        ]);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'label' => false
        ]);
    }

    public function buildView(FormView $view, FormInterface $form, array $options)
    {
        $view->vars['label'] = false;
    }

    public function getBlockPrefix(): string
    {
        return 'tulia_editor';
    }
}
