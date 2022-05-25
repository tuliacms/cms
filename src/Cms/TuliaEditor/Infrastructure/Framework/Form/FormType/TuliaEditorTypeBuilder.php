<?php

declare(strict_types=1);

namespace Tulia\Cms\TuliaEditor\Infrastructure\Framework\Form\FormType;

use Tulia\Cms\Content\Attributes\Domain\WriteModel\Model\Attribute;
use Tulia\Cms\Content\Type\Domain\ReadModel\FieldTypeBuilder\AbstractFieldTypeBuilder;
use Tulia\Cms\Content\Type\Domain\ReadModel\Model\Field;

/**
 * @author Adam Banaszkiewicz
 */
class TuliaEditorTypeBuilder extends AbstractFieldTypeBuilder
{
    /**
     * @return array<string, string>
     */
    public function buildValueFromAttribute(Field $field, Attribute $attribute)
    {
        return [
            'tulia_editor_structure' => json_encode($attribute->getPayload()),
            'tulia_editor_instance' => $attribute->getValue(),
        ];
    }

    /**
     * @param array $value
     */
    public function buildAttributeFromValue(Field $field, Attribute $attribute, $value): Attribute
    {
        $attribute = $attribute->withValue($value['tulia_editor_instance']);

        if ($value['tulia_editor_structure']) {
            $attribute = $attribute->withPayload(json_decode($value['tulia_editor_structure'], true, JSON_THROW_ON_ERROR));
        }

        return $attribute;
    }
}
