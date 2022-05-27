<?php

declare(strict_types=1);

namespace Tulia\Cms\Content\Attributes\Infrastructure\Importer;

use Tulia\Cms\Content\Attributes\Domain\WriteModel\Model\Attribute;
use Tulia\Cms\Content\Type\Domain\ReadModel\Model\ContentType;
use Tulia\Component\Importer\Structure\ObjectData;

/**
 * @author Adam Banaszkiewicz
 */
class ObjectDataToAttributesTransformer
{
    private ContentType $contentType;
    /** @var ObjectData[] */
    private array $objectDataList = [];

    public function __construct(ContentType $contentType)
    {
        $this->contentType = $contentType;
    }

    /**
     * @param ObjectData[] $data
     */
    public function useObjectData(array $data): void
    {
        $this->objectDataList += $data;
    }

    public function useAdditionalData(array $data): void
    {
        foreach ($data as $key => $val) {
            $this->objectDataList[$key] = $this->appendAttribute($key, $val);
        }
    }

    public function transform(): array
    {
        $attributes = [];

        foreach ($this->objectDataList as $attribute) {
            if ($this->contentType->hasField($attribute['name']) === false) {
                continue;
            }

            $field = $this->contentType->getField($attribute['name']);

            $attributes[$attribute['name']] = new Attribute(
                $attribute['name'],
                $attribute['uri'],
                $attribute['value'],
                $attribute['compiled_value'] ?? null,
                $attribute['payload'] ?? [],
                $field->getFlags() + [
                    'multilingual' => $field->isMultilingual(),
                    'non_scalar_value' => $field->hasNonscalarValue(),
                ]
            );
        }

        return $attributes;
    }

    /**
     * @param mixed $value
     */
    private function appendAttribute(string $attributeName, $value): array
    {
        $field = $this->contentType->getField($attributeName);

        return [
            'name' => $attributeName,
            'uri' => $attributeName,
            'value' => $value,
            'is_renderable' => $field->hasFlag('compilable'),
            'has_nonscalar_value' => $field->hasNonscalarValue(),
        ];
    }
}
