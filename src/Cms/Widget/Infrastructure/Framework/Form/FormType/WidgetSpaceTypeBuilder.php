<?php

declare(strict_types=1);

namespace Tulia\Cms\Widget\Infrastructure\Framework\Form\FormType;

use Symfony\Component\Validator\Constraints as Assert;
use Tulia\Cms\ContentBuilder\ContentType\Domain\ReadModel\FieldTypeBuilder\AbstractFieldTypeBuilder;
use Tulia\Cms\ContentBuilder\ContentType\Domain\ReadModel\Model\ContentType;
use Tulia\Cms\ContentBuilder\ContentType\Domain\ReadModel\Model\Field;
use Tulia\Component\Theme\ManagerInterface;

/**
 * @author Adam Banaszkiewicz
 */
class WidgetSpaceTypeBuilder extends AbstractFieldTypeBuilder
{
    protected ManagerInterface $themeManager;

    public function __construct(ManagerInterface $themeManager)
    {
        $this->themeManager = $themeManager;
    }

    public function buildOptions(Field $field, array $options, ContentType $contentType): array
    {
        $theme = $this->themeManager->getTheme();
        $spaces = [];

        if ($theme->hasConfig()) {
            $spaces = $theme->getConfig()->getRegisteredWidgetSpaces();
            $spaces = array_combine(
                array_map(function ($item) {
                    return $item['label'];
                }, $spaces),
                array_map(function ($item) {
                    return $item['name'];
                }, $spaces),
            );
        }

        $options['choices'] = $spaces;
        $options['choice_translation_domain'] = false;
        $options['constraints'][] = new Assert\NotBlank();
        $options['constraints'][] = new Assert\Choice([ 'choices' => $spaces ]);

        return $options;
    }
}
