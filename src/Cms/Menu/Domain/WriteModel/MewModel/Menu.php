<?php

declare(strict_types=1);

namespace Tulia\Cms\Menu\Domain\WriteModel\MewModel;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Tulia\Cms\Menu\Domain\WriteModel\Event\MenuCreated;
use Tulia\Cms\Menu\Domain\WriteModel\Event\MenuDeleted;
use Tulia\Cms\Menu\Domain\WriteModel\Event\MenuUpdated;
use Tulia\Cms\Menu\Domain\WriteModel\Exception\MenuItemDoesntExistsException;
use Tulia\Cms\Menu\Domain\WriteModel\Exception\ParentItemReccurencyException;
use Tulia\Cms\Shared\Domain\WriteModel\Model\AbstractAggregateRoot;

/**
 * @author Adam Banaszkiewicz
 * @final
 */
class Menu extends AbstractAggregateRoot
{
    /** @var ArrayCollection<int, Item> */
    private Collection $items;

    private function __construct(
        private string $id,
        private string $name
    ) {
        $this->items = new ArrayCollection([Item::createRoot($this)]);
        $this->recordThat(new MenuCreated($this->id));
    }

    public static function create(
        string $id,
        string $name
    ) : self {
        return new self($id, $name);
    }

    public function getId(): string
    {
        return $this->id;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function rename(string $name): void
    {
        $this->name = $name;
        $this->recordThat(new MenuUpdated($this->id));
    }

    public function delete(): void
    {
        $this->recordThat(new MenuDeleted($this->id));
    }

    public function createItem(array $locales, string $locale, string $name): Item
    {
        $root = $this->getRoot();
        $item = Item::create($this, $root, $locales, $locale, $name);
        $root->addChild($item);

        return $item;
    }

    public function getItem(string $id): Item
    {
        $root = $this->getRoot();

        if ($root->getId() === $id) {
            return $root;
        }

        $item = $root->findItem($id);

        if (!$item) {
            throw MenuItemDoesntExistsException::fromId($this->id, $id);
        }

        return $item;
    }

    public function itemsToArray(string $locale, string $defaultLocale): array
    {
        $result = [];

        foreach ($this->items->toArray() as $item) {
            $result[] = $item->toArray($locale, $defaultLocale);
        }

        return $result;
    }

    public function updateHierarchy(array $hierarchy): void
    {
        $root = $this->getRoot();
        $rebuildedHierarchy = [];

        foreach ($hierarchy as $child => $parentId) {
            $rebuildedHierarchy[$parentId ?: $root->getId()][] = $child;
        }

        foreach ($rebuildedHierarchy as $parentId => $items) {
            foreach ($items as $level => $id) {
                $item = $this->getItem($id);
                $this->moveToParent($item, $this->getItem($parentId));
                $item->moveToPosition($level + 1);
                $this->detectParentReccurency($item);
            }
        }
    }

    public function moveToParent(Item $item, Item $newParent): void
    {
        $parent = $item->getParent();

        if ($parent === $newParent) {
            return;
        }

        $parent->removeItem($item);

        $newParent->addChild($item);
    }

    public function deleteItem(string $itemId): void
    {
        $item = $this->getItem($itemId);
        $item->detachFromParent();
    }

    /**
     * @throws ParentItemReccurencyException
     */
    private function detectParentReccurency(Item $item): void
    {
        // @todo Implement recurrency detection.
    }

    private function getRoot(): Item
    {
        foreach ($this->items->toArray() as $item) {
            if ($item->isRoot()) {
                return $item;
            }
        }

        throw new \DomainException(sprintf('Expect the Menu %s has Root item, but nothing found.', $this->id));
    }
}
