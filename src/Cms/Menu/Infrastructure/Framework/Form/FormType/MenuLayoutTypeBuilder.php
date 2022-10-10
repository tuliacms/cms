<?php

declare(strict_types=1);

namespace Tulia\Cms\Menu\Infrastructure\Framework\Form\FormType;

use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Contracts\Translation\TranslatorInterface;
use Tulia\Cms\Content\Type\Domain\ReadModel\FieldTypeBuilder\AbstractFieldTypeBuilder;
use Tulia\Cms\Content\Type\Domain\ReadModel\Model\ContentType;
use Tulia\Cms\Content\Type\Domain\ReadModel\Model\Field;

/**
 * @author Adam Banaszkiewicz
 */
class MenuLayoutTypeBuilder extends AbstractFieldTypeBuilder
{
    public function __construct(
        private readonly TranslatorInterface $translator,
    ) {
    }

    public function buildOptions(Field $field, array $options, ContentType $contentType): array
    {
        $layout = [
            $this->translator->trans('horizontal', [], 'menu') => 'horizontal',
            $this->translator->trans('vertical', [], 'menu') => 'vertical',
        ];

        $options['choices'] = $layout;
        $options['choice_translation_domain'] = false;
        $options['constraints'][] = new Assert\NotBlank();
        $options['constraints'][] = new Assert\Choice([ 'choices' => $layout ]);

        return $options;
    }
}
