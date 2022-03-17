<?php

declare(strict_types=1);

namespace Tulia\Cms\Attributes\Domain\WriteModel\Model;

/**
 * @author Adam Banaszkiewicz
 */
interface AttributesAwareInterface
{
    /**
     * @param Attribute[] $attributes
     */
    public function updateAttributes(array $attributes): void;

    /**
     * @return Attribute[]
     */
    public function getAttributes(): array;

    public function addAttribute(Attribute $attribute): void;

    public function hasAttribute(string $uri): bool;

    public function removeAttribute(string $uri): void;

    /**
     * @param $default
     * @return mixed
     */
    public function getAttribute(string $uri, $default = null);
}
