<?php

declare(strict_types=1);

namespace Tulia\Cms\ContactForm\Domain\FieldType\Core;

use Symfony\Component\Validator\Constraints\NotBlank;
use Tulia\Cms\ContactForm\Domain\FieldType\AbstractFieldParser;
use Tulia\Component\Shortcode\ShortcodeInterface;

/**
 * @author Adam Banaszkiewicz
 */
class SelectParser extends AbstractFieldParser
{
    /**
     * {@inheritdoc}
     */
    public function getAlias(): string
    {
        return 'select';
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

        $choices = explode('|', $shortcode->getParameter('choices', ''));
        $choices = array_flip($choices);

        return [
            'name' => $shortcode->getParameter('name'),
            'type' => SelectType::class,
            'options' => [
                'constraints' => $constraintsRaw,
                'label' => $shortcode->getParameter('label'),
                'choices' => $choices,
                'data' => $choices[$shortcode->getParameter('selected')] ?? null,
            ],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function getDefinition(): array
    {
        return [
            'name' => 'Select',
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
                'choices' => [
                    'name' => 'List of choices separated with "|".',
                    'type' => 'collection',
                    'required' => true,
                    'multilingual' => true,
                ],
                'constraints' => [
                    'name' => 'Validation constraints for this field.',
                    'type' => 'collection',
                    'required' => false,
                    'collection' => [
                        'required' => 'Makes field required to submit form.',
                    ],
                ],
            ],
        ];
    }
}
