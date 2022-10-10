<?php

declare(strict_types=1);

namespace Tulia\Cms\ContactForm\Domain\FieldType\Core;

use Tulia\Cms\ContactForm\Domain\FieldType\AbstractFieldType;

/**
 * @author Adam Banaszkiewicz
 */
class TextareaType extends AbstractFieldType
{
    public function getAlias(): string
    {
        return 'textarea';
    }

    public function getFormType(): string
    {
        return \Symfony\Component\Form\Extension\Core\Type\TextareaType::class;
    }

    public function buildOptions(array $options): array
    {
        $options['attr']['placeholder'] = $options['placeholder'];

        unset($options['placeholder']);

        return $options;
    }
}
