<?php

declare(strict_types=1);

namespace Tulia\Cms\ContactForm\Domain\FieldType\Core;

use Tulia\Cms\ContactForm\Domain\FieldType\AbstractFieldType;

/**
 * @author Adam Banaszkiewicz
 */
class SubmitType extends AbstractFieldType
{
    /**
     * {@inheritdoc}
     */
    public function getAlias(): string
    {
        return 'submit';
    }

    /**
     * {@inheritdoc}
     */
    public function buildOptions(array $options): array
    {
        $options['attr']['class'] = $options['htmlclass'];
        unset($options['htmlclass']);

        return $options;
    }

    /**
     * {@inheritdoc}
     */
    public function getFormType(): string
    {
        return \Symfony\Component\Form\Extension\Core\Type\SubmitType::class;
    }
}
