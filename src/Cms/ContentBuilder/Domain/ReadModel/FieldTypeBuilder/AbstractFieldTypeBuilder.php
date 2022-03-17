<?php

declare(strict_types=1);

namespace Tulia\Cms\ContentBuilder\Domain\ReadModel\FieldTypeBuilder;

use Tulia\Cms\Attributes\Domain\WriteModel\Model\Attribute;
use Tulia\Cms\ContentBuilder\Domain\ReadModel\Model\ContentType;
use Tulia\Cms\ContentBuilder\Domain\ReadModel\Model\Field;

/**
 * @author Adam Banaszkiewicz
 */
abstract class AbstractFieldTypeBuilder implements FieldTypeBuilderInterface
{
    public function buildOptions(Field $field, array $options, ContentType $contentType): array
    {
        return $options;
    }

    /**
     * @return mixed
     */
    public function buildValueFromAttribute(Field $field, Attribute $attribute)
    {
        return $attribute->getValue();
    }

    /**
     * @param mixed $value
     */
    public function buildAttributeFromValue(Field $field, Attribute $attribute, $value): Attribute
    {
        return $attribute;
    }
}
