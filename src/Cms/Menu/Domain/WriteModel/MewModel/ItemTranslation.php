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
    private bool $visibility = true;
    private string $name = '';

    public function __construct(
        private Item $item,
        private string $locale
    ) {
    }

    public function isFor(string $locale): bool
    {
        return $this->locale === $locale;
    }

    public function renameTo(string $name): void
    {
        $this->name = $name;
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
