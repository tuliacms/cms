<?php

declare(strict_types=1);

namespace Tulia\Cms\Taxonomy\Domain\WriteModel\Model;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Tulia\Cms\Content\Attributes\Domain\WriteModel\Model\Attribute as CoreAttribute;
use Tulia\Cms\Content\Attributes\Domain\WriteModel\Model\AttributesAwareAggregateTrait;

/**
 * @author Adam Banaszkiewicz
 * @final
 */
class TermTranslation
{
    use AttributesAwareAggregateTrait;

    private string $id;
    public bool $visibility = true;
    public string $name = '';
    /** @var Attribute[] */
    private Collection $attributes;
    private bool $hasBeenTranslatedRightNow = false;

    public function __construct(
        private Term $term,
        public readonly string $locale,
        public bool $translated,
        string $name,
        string $creatingLocale,
    ) {
        $this->attributes = new ArrayCollection();
        $this->name = $name;

        if ($this->locale === $creatingLocale) {
            $this->translated = true;
        }
    }

    public function rename(string $name): void
    {
        $this->name = $name;

        if (false === $this->translated) {
            $this->translated = true;
            $this->hasBeenTranslatedRightNow = true;
        }
    }

    public function isTranslated(): bool
    {
        return $this->translated;
    }

    public function hasBeenTranslatedRightNow(): bool
    {
        $hasBeenTranslatedRightNow = $this->hasBeenTranslatedRightNow;
        $this->hasBeenTranslatedRightNow = false;

        return $hasBeenTranslatedRightNow;
    }

    public function toArray(): array
    {
        return [
            'name' => $this->name,
            'visibility' => $this->visibility,
            'translated' => $this->translated,
            'attributes' => $this->attributesToArray(),
        ];
    }

    protected function noticeThatAttributeHasBeenAdded(CoreAttribute $attribute): void {}

    protected function noticeThatAttributeHasBeenRemoved(CoreAttribute $attribute): void {}

    protected function noticeThatAttributeHasBeenUpdated(CoreAttribute $attribute): void {}

    protected function factoryAttributeFromCore(CoreAttribute $attribute): Attribute
    {
        return Attribute::fromCore($this, $attribute, $this->locale);
    }
}
