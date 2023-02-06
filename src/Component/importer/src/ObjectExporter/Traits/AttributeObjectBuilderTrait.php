<?php

declare(strict_types=1);

namespace Tulia\Component\Importer\ObjectExporter\Traits;

use Tulia\Cms\Content\Attributes\Domain\ReadModel\Service\AttributesFinderInterface;
use Tulia\Component\Importer\Structure\ObjectDataFactory;

/**
 * @property ObjectDataFactory $objectDataFactory
 * @author Adam Banaszkiewicz
 */
trait AttributeObjectBuilderTrait
{
    protected function buildAttributes(
        AttributesFinderInterface $attributesFinder,
        string $ownerId,
        string $locale,
    ): array {
        $attributes = [];

        foreach ($attributesFinder->query($ownerId, $locale) as $attribute) {
            $attr = $this->objectDataFactory->createEmpty('Attribute');
            $attr['payload'] = serialize($attribute['payload']);
            $attr['code'] = $attribute['code'];
            $attr['uri'] = $attribute['uri'];
            $attr['value'] = $attribute['value'][0];

            $attributes[] = $attr;
        }

        return $attributes;
    }
}
