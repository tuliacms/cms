<?php

declare(strict_types=1);

namespace Tulia\Cms\Content\Attributes\Domain\WriteModel\Model;

use Doctrine\Common\Collections\Collection;

/**
 * @author Adam Banaszkiewicz
 * @method void noticeThatAttributeHasBeenAdded(Attribute $attribute)
 * @method void noticeThatAttributeHasBeenRemoved(Attribute $attribute)
 * @method void noticeThatAttributeHasBeenUpdated(Attribute $attribute)
 * @method Attribute factoryAttributeFromCore(Attribute $attribute)
 * @property Collection $attributes
 */
trait AttributesAwareAggregateTrait
{
    public function persistAttributes(Attribute ...$sourceAttributes): void
    {
        $attributes = $this->repackAttributes($sourceAttributes);

        $keysNew = array_keys($attributes);
        $keysOld = array_keys($this->attributesToArray());

        $toAdd = array_diff($keysNew, $keysOld);
        $toRemove = array_diff($keysOld, $keysNew);
        $toUpdate = array_intersect($keysNew, $keysOld);

        foreach ($toRemove as $uri) {
            $this->removeAttribute($uri);
        }

        foreach ($toAdd as $uri) {
            if ($attributes[$uri]->isEmpty()) {
                $this->removeAttribute($uri);
            } else {
                $this->addAttribute($this->factoryAttributeFromCore($attributes[$uri]));
            }
        }

        foreach ($toUpdate as $uri) {
            if ($attributes[$uri]->isEmpty()) {
                $this->removeAttribute($uri);
            } else {
                $this->addAttribute($this->factoryAttributeFromCore($attributes[$uri]));
            }
        }
    }

    public function hasAttribute(string $uri): bool
    {
        foreach ($this->attributes as $attribute) {
            if ($attribute->getUri() === $uri) {
                return true;
            }
        }

        return false;
    }

    public function getAttribute(string $uri): Attribute
    {
        foreach ($this->attributes as $attribute) {
            if ($attribute->getUri() === $uri) {
                return $attribute;
            }
        }

        throw new \DomainException(sprintf('Attribute %s not found.', $uri));
    }

    public function addAttribute(Attribute $attribute): void
    {
        /** @var null|Attribute $found */
        $found = null;

        foreach ($this->attributes as $pretendend) {
            if ($pretendend->getUri() === $attribute->getUri()) {
                $found = $pretendend;
            }
        }

        if (!$found) {
            $this->attributes->add($attribute);
            $this->noticeThatAttributeHasBeenAdded($attribute);
            return;
        }

        if ($found->equals($attribute) === false) {
            $found->detach();
            $this->attributes->removeElement($found);
            $this->attributes->add($attribute);
            $this->noticeThatAttributeHasBeenUpdated($attribute);
        }
    }

    public function removeAttribute(string $uri): void
    {
        foreach ($this->attributes as $attribute) {
            if ($attribute->getUri() === $uri) {
                $attribute->detach();
                $this->attributes->removeElement($attribute);
                $this->noticeThatAttributeHasBeenRemoved($attribute);
            }
        }
    }

    protected function repackAttributes(array $attributes): array
    {
        $repackedAttributes = [];

        foreach ($attributes as $attribute) {
            $repackedAttributes[$attribute->getUri()] = $attribute;
        }

        return $repackedAttributes;
    }

    protected function attributesToArray(): array
    {
        $attributes = [];

        foreach ($this->attributes->toArray() as $attribute) {
            $attributes[$attribute->getUri()] = $attribute;
        }

        return $attributes;
    }
}
