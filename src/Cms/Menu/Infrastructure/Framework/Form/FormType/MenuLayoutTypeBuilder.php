<?php

declare(strict_types=1);

namespace Tulia\Cms\Menu\Infrastructure\Framework\Form\FormType;

use Symfony\Contracts\Translation\TranslatorInterface;
use Symfony\Component\Validator\Constraints as Assert;
use Tulia\Cms\ContentBuilder\Domain\ReadModel\FieldTypeBuilder\AbstractFieldTypeBuilder;
use Tulia\Cms\ContentBuilder\Domain\ReadModel\Model\ContentType;
use Tulia\Cms\ContentBuilder\Domain\ReadModel\Model\Field;

/**
 * @author Adam Banaszkiewicz
 */
class MenuLayoutTypeBuilder extends AbstractFieldTypeBuilder
{
    private TranslatorInterface $translator;

    public function __construct(TranslatorInterface $translator)
    {
        $this->translator = $translator;
    }

    public function buildOptions(Field $field, array $options, ContentType $contentType): array
    {
        $layout = [
            $this->translator->trans('horizontal', [], 'menu') => 0,
            $this->translator->trans('vertical', [], 'menu') => 1,
        ];

        $options['choices'] = $layout;
        $options['choice_translation_domain'] = false;
        $options['constraints'][] = new Assert\NotBlank();
        $options['constraints'][] = new Assert\Choice([ 'choices' => $layout ]);

        return $options;
    }
}
