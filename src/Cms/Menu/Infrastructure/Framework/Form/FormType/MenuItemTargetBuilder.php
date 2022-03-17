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
class MenuItemTargetBuilder extends AbstractFieldTypeBuilder
{
    protected TranslatorInterface $translator;

    public function __construct(TranslatorInterface $translator)
    {
        $this->translator = $translator;
    }

    public function buildOptions(Field $field, array $options, ContentType $contentType): array
    {
        $itemTargets = [
            $this->translator->trans('itemTargetAuto', [], 'menu')  => '',
            $this->translator->trans('itemTargetSelf', [], 'menu')  => '_self',
            $this->translator->trans('itemTargetBlank', [], 'menu') => '_blank',
        ];

        $options['choices'] = $itemTargets;
        $options['choice_translation_domain'] = false;
        $options['constraints'][] = new Assert\Choice([ 'choices' => $itemTargets ]);

        return $options;
    }
}
