<?php

declare(strict_types=1);

namespace Tulia\Cms\Widget\Domain\WriteModel\Model;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Tulia\Cms\Content\Attributes\Domain\WriteModel\Model\Attribute as CoreAttribute;
use Tulia\Cms\Content\Attributes\Domain\WriteModel\Model\AttributesAwareAggregateTrait;

/**
 * @author Adam Banaszkiewicz
 * @final
 */
class WidgetTranslation
{
    use AttributesAwareAggregateTrait;

    private string $id;
    public bool $visibility = true;
    public bool $translated = false;
    /** @var Attribute[]|ArrayCollection<int, Attribute> */
    private Collection $attributes;

    public function __construct(
        private widget $widget,
        private string $locale,
        string $creatingLocale,
        public ?string $title = null,
    ) {
        if ($this->locale === $creatingLocale) {
            $this->translated = true;
        }

        $this->attributes = new ArrayCollection();
    }

    public static function cloneToLocale(self $trans, string $locale): self
    {
        return new self($trans->widget, $locale, $locale, $trans->title);
    }

    public function toArray(): array
    {
        return [
            'visibility' => $this->visibility,
            'translated' => $this->translated,
            'locale' => $this->locale,
            'title' => $this->title,
            'attributes' => $this->attributesToArray(),
        ];
    }

    public function isFor(string $locale): bool
    {
        return $this->locale === $locale;
    }

    public function isTranslated(): bool
    {
        return $this->translated;
    }

    protected function noticeThatAttributeHasBeenAdded(CoreAttribute $attribute): void {}

    protected function noticeThatAttributeHasBeenRemoved(CoreAttribute $attribute): void {}

    protected function noticeThatAttributeHasBeenUpdated(CoreAttribute $attribute): void {}

    protected function factoryAttributeFromCore(CoreAttribute $attribute): Attribute
    {
        return Attribute::fromCore($this, $attribute, $this->locale);
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
