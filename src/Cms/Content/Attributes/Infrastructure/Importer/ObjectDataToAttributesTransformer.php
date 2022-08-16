<?php

declare(strict_types=1);

namespace Tulia\Cms\Content\Attributes\Infrastructure\Importer;

use Tulia\Cms\Content\Attributes\Domain\WriteModel\Model\Attribute;
use Tulia\Cms\Content\Type\Domain\ReadModel\Model\ContentType;

/**
 * @author Adam Banaszkiewicz
 */
class ObjectDataToAttributesTransformer
{
    public function __construct(
        private ContentType $contentType
    ) {
    }

    public function transform(array $source): array
    {
        $attributes = [];

        foreach ($source as $attribute) {
            if ($this->contentType->hasField($attribute['code']) === false) {
                continue;
            }

            $field = $this->contentType->getField($attribute['code']);
            $flags = $field->getFlags();

            $field->isMultilingual() ? $flags[] = 'multilingual' : null;
            $field->isNonscalarValue() ? $flags[] = 'nonscalar_value' : null;

            if (isset($attribute['payload'])) {
                $attribute['payload'] = unserialize($attribute['payload'], ['allowed_classes' => []]);
            } else {
                $attribute['payload'] = [];
            }

            $attributes[$attribute['code']] = new Attribute(
                $attribute['code'],
                $attribute['uri'],
                $attribute['value'],
                $attribute['compiled_value'] ?? null,
                $attribute['payload'],
                $flags
            );
        }

        return $attributes;
    }
}
