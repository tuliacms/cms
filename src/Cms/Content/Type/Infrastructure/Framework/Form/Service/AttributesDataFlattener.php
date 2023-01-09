<?php

declare(strict_types=1);

namespace Tulia\Cms\Content\Type\Infrastructure\Framework\Form\Service;

use Tulia\Cms\Content\Attributes\Domain\WriteModel\Model\Attribute;
use Tulia\Cms\Content\Type\Domain\ReadModel\Model\ContentType;
use Tulia\Cms\Content\Type\Domain\ReadModel\Model\Field;
use Tulia\Cms\Content\Type\Domain\ReadModel\Service\FieldTypeMappingRegistry;

/**
 * @author Adam Banaszkiewicz
 */
final class AttributesDataFlattener
{
    public function __construct(
        private readonly FieldTypeMappingRegistry $mappingRegistry,
    ) {
    }

    public function flattenAttributes(array $attributes, ContentType $contentType): array
    {
        return $this->flattenAttributesValues($contentType->getFields(), $attributes);
    }

    /**
     * @param Field[] $fields
     * @param Attribute[]|array<string, Attribute[]> $attributes
     */
    private function flattenAttributesValues(array $fields, array $attributes): array
    {
        $result = [];

        foreach ($fields as $code => $field) {
            if (isset($attributes[$code]) === false) {
                continue;
            }

            $attribute = $attributes[$code];

            if ($attribute instanceof Attribute) {
                $typeBuilder = $this->mappingRegistry->getTypeBuilder($field->getType());

                if ($typeBuilder) {
                    $result[$code] = $typeBuilder->buildValueFromAttribute($field, $attribute);
                } else {
                    $result[$code] = $attribute->getValue()->toRaw();

                    if (\is_array($result[$code]) && !$this->mappingRegistry->get($field->getType())['is_multiple']) {
                        $result[$code] = reset($result[$code]);
                    }
                }
            }

            if (\is_array($attribute)) {
                foreach ($attribute as $sk => $sv) {
                    $result[$code][$sk] = $this->flattenAttributesValues($field->getChildren(), $sv);
                }
            }
        }

        return $result;
    }
}
