<?php

declare(strict_types=1);

namespace Tulia\Cms\ContactForm\Domain\FieldType\Core;

use Symfony\Component\Validator\Constraints\Email;
use Tulia\Cms\ContactForm\Domain\FieldType\AbstractFieldType;

/**
 * @author Adam Banaszkiewicz
 */
class EmailType extends AbstractFieldType
{
    public function getAlias(): string
    {
        return 'email';
    }

    public function getFormType(): string
    {
        return \Symfony\Component\Form\Extension\Core\Type\EmailType::class;
    }

    public function buildOptions(array $options): array
    {
        /**
         * By default, add Email constraint for all Email field types.
         */
        $options['constraints'][] = new Email();
        $options['attr']['placeholder'] = $options['placeholder'];

        // Remove special option, used by sender.
        unset($options['sender'], $options['placeholder']);

        return $options;
    }
}
