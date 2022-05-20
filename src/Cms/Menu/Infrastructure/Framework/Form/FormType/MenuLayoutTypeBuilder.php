<?php

declare(strict_types=1);

namespace Tulia\Cms\Menu\Infrastructure\Framework\Form\FormType;

use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Contracts\Translation\TranslatorInterface;
use Tulia\Cms\ContentBuilder\ContentType\Domain\ReadModel\FieldTypeBuilder\AbstractFieldTypeBuilder;
use Tulia\Cms\ContentBuilder\ContentType\Domain\ReadModel\Model\ContentType;
use Tulia\Cms\ContentBuilder\ContentType\Domain\ReadModel\Model\Field;

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
