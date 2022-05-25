<?php

declare(strict_types=1);

namespace Tulia\Cms\Content\Attributes\Domain\WriteModel;

use Tulia\Cms\Content\Attributes\Domain\WriteModel\Model\Attribute;

/**
 * @property Attribute[] $attributes
 * @author Adam Banaszkiewicz
 */
trait AttributesAwareTrait
{
    /**
     * @return Attribute[]
     */
    public function getAttributes(): array
    {
        return $this->attributes;
    }

    /**
     * @param Attribute|mixed $default
     * @return mixed
     */
    public function getAttribute(string $uri, $default = null)
    {
        return $this->attributes[$uri] ?? $default;
    }

    public function hasAttribute(string $uri): bool
    {
        return isset($this->attributes[$uri]);
    }

    /**
     * @param Attribute[] $attributes
     */
    public function updateAttributes(array $attributes): void
    {
        foreach ($attributes as $attribute) {
            $this->addAttribute($attribute);
        }
    }

    public function addAttribute(Attribute $attribute): void
    {
        $this->attributes[$attribute->getUri()] = $attribute;
    }

    public function removeAttribute(string $uri): void
    {
        unset($this->attributes[$uri]);
    }
}
