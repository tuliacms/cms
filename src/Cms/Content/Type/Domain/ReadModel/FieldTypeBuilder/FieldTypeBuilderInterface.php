<?php

declare(strict_types=1);

namespace Tulia\Cms\Content\Type\Domain\ReadModel\FieldTypeBuilder;

use Tulia\Cms\Content\Attributes\Domain\WriteModel\Model\Attribute;
use Tulia\Cms\Content\Type\Domain\ReadModel\Model\ContentType;
use Tulia\Cms\Content\Type\Domain\ReadModel\Model\Field;

/**
 * @author Adam Banaszkiewicz
 */
interface FieldTypeBuilderInterface
{
    public function buildOptions(Field $field, array $options, ContentType $contentType): array;

    public function buildValueFromAttribute(Field $field, Attribute $attribute): mixed;

    public function buildAttributeFromValue(Field $field, Attribute $attribute, mixed $value): Attribute;
}
