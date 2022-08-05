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
    public const ROOT_ID = '00000000-0000-0000-0000-000000000000';
    public const ROOT_LEVEL = 0;

    private string $id;
    private int $position = 99999;
    private ?string $identity = null;
    private ?string $hash = null;
    private ?string $target = null;
    /** @var ArrayCollection<int, Item> */
    private Collection $translations;
    /** @var ArrayCollection<int, Item> */
    private Collection $items;

    private function __construct(
        private Menu $menu,
        private string $type = 'simple:homepage',
        private bool $isRoot = false,
        private ?Item $parent = null,
        private int $level = 0,
    ) {
        $this->translations = new ArrayCollection();
        $this->items = new ArrayCollection();
    }

    public static function createRoot(Menu $menu): self
    {
        $root = new self($menu, 'root', true);
        $root->position = 0;

        return $root;
    }

    public static function create(Menu $menu, Item $parent): self
    {
        return new self(
            $menu,
            parent: $parent,
            level: $parent->level + 1
        );
    }

    public function toArray(string $locale, string $defaultLocale): array
    {
        if ($this->isTranslatedTo($locale)) {
            $translation = $this->trans($locale)->toArray();
            $isTranslated = true;
        } elseif ($this->isTranslatedTo($defaultLocale)) {
            $translation = $this->trans($defaultLocale)->toArray();
            $isTranslated = false;
        }

        return [
            'id' => $this->id,
            'position' => $this->position,
            'identity' => $this->identity,
            'hash' => $this->hash,
            'target' => $this->target,
            'type' => $this->type,
            'parent' => $this->parent?->id,
            'level' => $this->level,
            'name' => $translation['name'],
            'locale' => $translation['locale'],
            'visibility' => $translation['visibility'],
            'translated' => $isTranslated,
        ];
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

    public function isTranslatedTo(string $locale): bool
    {
        foreach ($this->translations as $translation) {
            if ($translation->isFor($locale)) {
                return true;
            }
        }

        return false;
    }

    public function translate(string $locale): ItemTranslation
    {
        if ($this->isTranslatedTo($locale)) {
            $translation = $this->trans($locale);
        } else {
            $translation = new ItemTranslation($this, $locale);
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

    public function addChild(Item $child): void
    {
        $this->items->add($child);
        $this->sortItems();
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
}
