<?php

declare(strict_types=1);

namespace Tulia\Cms\Content\Type\Domain\ReadModel\FieldTypeBuilder;

use Tulia\Cms\Content\Attributes\Domain\WriteModel\Model\Attribute;
use Tulia\Cms\Content\Type\Domain\ReadModel\Model\ContentType;
use Tulia\Cms\Content\Type\Domain\ReadModel\Model\Field;

/**
 * @author Adam Banaszkiewicz
 */
abstract class AbstractFieldTypeBuilder implements FieldTypeBuilderInterface
{
    public function buildOptions(Field $field, array $options, ContentType $contentType): array
    {
        return $options;
    }

    public function buildValueFromAttribute(Field $field, Attribute $attribute): mixed
    {
        $value = $attribute->getValue()->toRaw();

        if ($field->isMultiple()) {
            return $value;
        }

        return reset($value);
    }

    public function buildAttributeFromValue(Field $field, Attribute $attribute, mixed $value): Attribute
    {
        return $attribute->withValue($value);
    }
}
