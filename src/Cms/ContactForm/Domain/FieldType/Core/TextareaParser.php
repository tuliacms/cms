<?php

declare(strict_types=1);

namespace Tulia\Cms\ContactForm\Domain\FieldType\Core;

use Symfony\Component\Validator\Constraints\NotBlank;
use Tulia\Cms\ContactForm\Domain\FieldType\AbstractFieldParser;
use Tulia\Component\Shortcode\ShortcodeInterface;

/**
 * @author Adam Banaszkiewicz
 */
class TextareaParser extends AbstractFieldParser
{
    /**
     * {@inheritdoc}
     */
    public function getAlias(): string
    {
        return 'textarea';
    }

    /**
     * {@inheritdoc}
     */
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
            'type' => TextareaType::class,
            'options' => [
                'constraints' => $constraintsRaw,
                'label' => $shortcode->getParameter('label'),
                'help' => $shortcode->getParameter('help'),
            ],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function getDefinition(): array
    {
        return [
            'name' => 'Textarea',
            'options' => [
                'name' => [
                    'name' => 'Field codename. Must be unique across whole form.',
                    'type' => 'text',
                    'required' => true,
                ],
                'label' => [
                    'name' => 'Label showed in field form.',
                    'type' => 'text',
                    'required' => true,
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
