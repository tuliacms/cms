<?php

declare(strict_types=1);

namespace Tulia\Cms\Platform\Infrastructure\Framework\Form\FormType;

use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormView;
use Symfony\Component\Form\FormInterface;

/**
 * @author Adam Banaszkiewicz
 */
class DateTimeType extends AbstractType
{
    public function buildView(FormView $view, FormInterface $form, array $options)
    {
    }

    public function finishView(FormView $view, FormInterface $form, array $options)
    {
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'format'   => 'Y-m-d H:i:s',
            'compound' => false,
        ]);
    }

    public function getBlockPrefix(): string
    {
        return 'datetime';
    }
}
