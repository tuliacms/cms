<?php

declare(strict_types=1);

namespace Tulia\Cms\Menu\Domain\WriteModel\MewModel;

/**
 * @author Adam Banaszkiewicz
 * @final
 */
class ItemTranslation
{
    private string $id;
    public bool $visibility = true;
    public string $name = '';

    public function __construct(
        private Item $item,
        private string $locale,
        public bool $translated,
        string $name,
        string $creatingLocale,
    ) {
        $this->name = $name;

        if ($this->locale === $creatingLocale) {
            $this->translated = true;
        }
    }

    public static function cloneToLocale(ItemTranslation $trans, string $locale): self
    {
        return new self($trans->item, $locale, false, $trans->name, $locale);
    }

    public function isFor(string $locale): bool
    {
        return $this->locale === $locale;
    }

    public function isTranslated(): bool
    {
        return $this->translated;
    }

    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'locale' => $this->locale,
            'visibility' => $this->visibility,
            'name' => $this->name,
        ];
    }
}
