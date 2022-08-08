<?php

declare(strict_types=1);

namespace Tulia\Cms\Menu\Domain\WriteModel\MewModel;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Tulia\Cms\Menu\Domain\WriteModel\Exception\ItemTranslationDoesntExistsException;

/**
 * @author Adam Banaszkiewicz
 * @final
 */
class Item
{
    private string $id;
    private int $position = 99999;
    private ?string $identity = null;
    private ?string $hash = null;
    private ?string $target = null;
    /** @var ArrayCollection<int, ItemTranslation> */
    private Collection $translations;
    /** @var ArrayCollection<int, Item> */
    private Collection $items;

    private function __construct(
        private Menu $menu,
        string $creatingLocale,
        string $name,
        private string $type = 'simple:homepage',
        private bool $isRoot = false,
        private ?Item $parent = null,
        private int $level = 0,
        array $locales = [],
    ) {
        $translations = [];

        foreach ($locales as $locale) {
            $translations[] = new ItemTranslation($this, $locale, false, $name, $creatingLocale);
        }

        $this->items = new ArrayCollection();
        $this->translations = new ArrayCollection($translations);
    }

    public static function createRoot(Menu $menu): self
    {
        $root = new self($menu, '', '', 'root', true);
        $root->position = 0;

        return $root;
    }

    public static function create(
        Menu $menu,
        Item $parent,
        array $locales,
        string $creatingLocale,
        string $name
    ): self {
        return new self(
            $menu,
            creatingLocale: $creatingLocale,
            name: $name,
            parent: $parent,
            level: $parent->level + 1,
            locales: $locales,
        );
    }

    public function toArray(string $locale, string $defaultLocale): array
    {
        $translation = [];
        $isTranslated = false;

        if ($this->isTranslatedTo($locale)) {
            $translation = $this->trans($locale)->toArray();
            $isTranslated = true;
        } elseif ($this->isTranslatedTo($defaultLocale)) {
            $translation = $this->trans($defaultLocale)->toArray();
        }

        return [
            'id' => $this->id,
            'position' => $this->position,
            'identity' => $this->identity,
            'hash' => $this->hash,
            'target' => $this->target,
            'type' => $this->type,
            'parent_id' => $this->parent?->id,
            'level' => $this->level,
            'name' => $translation['name'] ?? '',
            'locale' => $translation['locale'] ?? $locale,
            'visibility' => $translation['visibility'] ?? false,
            'is_root' => $this->isRoot,
            'translated' => $isTranslated,
        ];
    }

    public function getId(): string
    {
        return $this->id;
    }

    public function isRoot(): bool
    {
        return $this->isRoot;
    }

    public function isChildOf(self $parent): bool
    {
        return $this->parent?->id === $parent->id;
    }

    public function getPosition(): int
    {
        return $this->position;
    }

    public function moveToPosition(int $position): void
    {
        $this->position = $position;
    }

    public function addChild(Item $child): void
    {
        $this->items->add($child);
        $child->parent = $this;
        $child->moveToPosition(9999);
        $child->level = $this->level + 1;
        $this->sortItems();
    }

    public function linksTo(string $type, string $identity, string $hash): void
    {
        $this->type = $type;
        $this->identity = $identity;
        $this->hash = $hash;
    }

    public function openInNewTab(): void
    {
        $this->target = '_blank';
    }

    public function openInSelfTab(): void
    {
        $this->target = '_self';
    }

    public function findItem(string $id): ?Item
    {
        foreach ($this->items as $item) {
            if ($item->id === $id) {
                return $item;
            }

            if ($child = $item->findItem($id)) {
                return $child;
            }
        }

        return null;
    }

    public function getParent(): Item
    {
        return $this->parent;
    }

    public function removeItem(Item $item): void
    {
        foreach ($this->items as $pretendent) {
            if ($item === $pretendent) {
                $this->items->removeElement($pretendent);
                $pretendent->parent = null;
            }
        }
    }

    public function renameTo(
        string $locale,
        string $defaultLocale,
        string $name
    ): void {
        $trans = $this->translation($locale, $defaultLocale);
        $trans->translated = true;
        $trans->name = $name;

        if ($locale === $defaultLocale) {
            foreach ($this->translations as $translation) {
                if (false === $translation->isTranslated()) {
                    $translation->name = $name;
                }
            }
        }
    }

    public function turnVisibilityOn(string $locale, string $defaultLocale): void
    {
        $trans = $this->translation($locale, $defaultLocale);
        $trans->visibility = true;

        if ($locale === $defaultLocale) {
            foreach ($this->translations as $translation) {
                if (false === $translation->isTranslated()) {
                    $translation->visibility = true;
                }
            }
        }
    }

    public function turnVisibilityOff(string $locale, string $defaultLocale): void
    {
        $trans = $this->translation($locale, $defaultLocale);
        $trans->visibility = false;

        if ($locale === $defaultLocale) {
            foreach ($this->translations as $translation) {
                if (false === $translation->isTranslated()) {
                    $translation->visibility = false;
                }
            }
        }
    }

    private function translation(string $locale, string $defaultLocale): ItemTranslation
    {
        if ($this->isTranslatedTo($locale)) {
            $translation = $this->trans($locale);
        } else {
            $translation = ItemTranslation::cloneToLocale(
                $this->trans($defaultLocale),
                $locale
            );

            $this->translations->add($translation);
        }

        /*$translation->setUpdateCallback(function () {
            $this->markAsUpdated();
        });*/

        return $translation;
    }

    private function trans(string $locale): ItemTranslation
    {
        foreach ($this->translations as $translation) {
            if ($translation->isFor($locale)) {
                /*$translation->setUpdateCallback(function () {
                    $this->markAsUpdated();
                });*/
                return $translation;
            }
        }

        throw ItemTranslationDoesntExistsException::fromLocale($this->id, $locale);
    }

    private function sortItems(): void
    {
        $items = $this->items->toArray();

        usort($items, static function (Item $a, Item $b) {
            return $a->getPosition() <=> $b->getPosition();
        });

        /**
         * @var Item $item
         */
        foreach ($items as $position => $item) {
            $item->moveToPosition($position + 1);
        }
    }

    private function isTranslatedTo(string $locale): bool
    {
        foreach ($this->translations as $translation) {
            if ($translation->isFor($locale)) {
                return true;
            }
        }

        return false;
    }

    public function detachFromParent(): void
    {
        $this->parent->items->removeElement($this);
        $this->parent = null;
    }
}
