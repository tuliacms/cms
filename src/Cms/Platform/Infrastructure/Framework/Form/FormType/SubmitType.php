<?php

declare(strict_types=1);

namespace Tulia\Cms\Platform\Infrastructure\Framework\Form\FormType;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * @author Adam Banaszkiewicz
 */
class SubmitType extends AbstractType
{
    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'label' => 'save',
            'icon'  => 'fas fa-save',
            'mapped' => false,
            'translation_domain' => 'messages',
        ]);
    }

    public function getBlockPrefix(): string
    {
        return 'tulia_submit';
    }
}
