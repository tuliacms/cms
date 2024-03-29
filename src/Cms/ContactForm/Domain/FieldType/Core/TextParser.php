<?php

declare(strict_types=1);

namespace Tulia\Cms\ContactForm\Domain\FieldType\Core;

use Symfony\Component\Validator\Constraints\NotBlank;
use Tulia\Cms\ContactForm\Domain\FieldType\AbstractFieldParser;
use Tulia\Component\Shortcode\ShortcodeInterface;

/**
 * @author Adam Banaszkiewicz
 */
class TextParser extends AbstractFieldParser
{
    public function getAlias(): string
    {
        return 'text';
    }

    public function parseShortcode(ShortcodeInterface $shortcode): array
    {
        $constraints = $this->parseConstraints($shortcode->getParameter('constraints'));
        $constraintsRaw = [];

        if (isset($constraints['required'])) {
            $constraintsRaw[] = [
                'name' => NotBlank::class,
            ];
        }

        return [
            'name' => $shortcode->getParameter('name'),
            'type' => TextType::class,
            'options' => [
                'constraints' => $constraintsRaw,
                'label' => $shortcode->getParameter('label'),
                'help' => $shortcode->getParameter('help'),
                'placeholder' => $shortcode->getParameter('placeholder'),
            ],
        ];
    }

    public function getDefinition(): array
    {
        return [
            'name' => 'Text',
            'options' => [
                'name' => [
                    'name' => 'Field codename. Must be unique across whole form.',
                    'type' => 'text',
                    'required' => true,
                ],
                'label' => [
                    'name' => 'Label showed in field form.',
                    'type' => 'text',
                    'multilingual' => true,
                ],
                'placeholder' => [
                    'name' => 'Placeholder showed inside the field.',
                    'type' => 'text',
                    'multilingual' => true,
                ],
                'help' => [
                    'name' => 'Help text showed under the field.',
                    'type' => 'text',
                    'required' => false,
                    'multilingual' => true,
                ],
                'constraints' => [
                    'name' => 'Validation constraints for this field.',
                    'type' => 'collection',
                    'required' => false,
                    'collection' => [
                        'required' => 'Makes field required to submit form.',
                        //'max:50' => 'Requires maximum length of text in this field to specified number.',
                    ],
                ],
            ],
        ];
    }
}
