<?php

declare(strict_types=1);

namespace Tulia\Cms\ContentBuilder\ContentType\Domain\ReadModel\FieldTypeBuilder;

use Tulia\Cms\ContentBuilder\Attributes\Domain\WriteModel\Model\Attribute;
use Tulia\Cms\ContentBuilder\ContentType\Domain\ReadModel\Model\ContentType;
use Tulia\Cms\ContentBuilder\ContentType\Domain\ReadModel\Model\Field;

/**
 * @author Adam Banaszkiewicz
 */
interface FieldTypeBuilderInterface
{
    public function buildOptions(Field $field, array $options, ContentType $contentType): array;

    /**
     * @return mixed
     */
    public function buildValueFromAttribute(Field $field, Attribute $attribute);

    /**
     * @param mixed $value
     */
    public function buildAttributeFromValue(Field $field, Attribute $attribute, $value): Attribute;
}