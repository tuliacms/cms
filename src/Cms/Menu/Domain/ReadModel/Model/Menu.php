<?php

declare(strict_types=1);

namespace Tulia\Cms\Menu\Domain\ReadModel\Model;

/**
 * @author Adam Banaszkiewicz
 */
class Menu
{
    protected string $id;
    protected string $name;
    /** @var Item[] */
    protected array $items = [];
    protected ?string $rootItemId = null;

    public static function buildFromArray(array $data): Menu
    {
        if (isset($data['id']) === false) {
            throw new \InvalidArgumentException('Menu ID must be provided.');
        }

        $menu = new self();
        $menu->rootItemId = $data['root_item_id'] ?? null;
        $menu->setId($data['id']);
        $menu->setName($data['name'] ?? '');

        foreach ($data['items'] ?? [] as $item) {
            $menu->items[] = Item::buildFromArray($item);
        }

        return $menu;
    }

    public function hasId(): bool
    {
        return (bool) $this->id;
    }

    public function hasItem(string $id): bool
    {
        foreach ($this->items as $item) {
            if ($item->getId() === $id) {
                return true;
            }
        }

        return false;
    }

    public function getId(): string
    {
        return $this->id;
    }

    public function setId(string $id): void
    {
        $this->id = $id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): void
    {
        $this->name = $name;
    }

    /**
     * @return Item[]
     */
    public function getItems(): array
    {
        return $this->items;
    }

    public function setItems(array $items): void
    {
        $this->items = $items;
    }

    public function getRootItemId(): ?string
    {
        return $this->rootItemId;
    }
}
