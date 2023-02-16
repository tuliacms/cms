<?php

declare(strict_types=1);

namespace Tulia\Cms\Menu\Domain\Builder\Hierarchy;

use Tulia\Cms\Menu\Domain\Builder\Identity\IdentityInterface;

/**
 * @author Adam Banaszkiewicz
 */
class Item
{
    private string $id;
    private int $level = 1;
    private string $label;
    private ?string $target;
    private ?string $hash;
    private ?string $link = null;
    private IdentityInterface $identity;
    private array $attributes = [];
    private array $children = [];

    public function getId(): string
    {
        return $this->id;
    }

    public function setId(string $id): void
    {
        $this->id = $id;
    }

    public function getChildren(): array
    {
        return $this->children;
    }

    public function setChildren(array $children): void
    {
        foreach ($children as $child) {
            $this->addChild($child);
        }
    }

    public function hasChildren(): bool
    {
        return $this->children !== [];
    }

    public function addChild(Item $child): void
    {
        $this->children[] = $child;
    }

    public function getLevel(): int
    {
        return $this->level;
    }

    public function setLevel(int $level): void
    {
        $this->level = $level;
    }

    public function isRoot(): bool
    {
        return $this->level === 1;
    }

    public function getLabel(): string
    {
        return $this->label;
    }

    public function setLabel(string $label): void
    {
        $this->label = $label;
    }

    public function getLink(): string
    {
        if ($this->link) {
            return $this->link;
        }

        $link = $this->identity->getLink();

        if ($this->hash && strpos($link, '#') === false) {
            $link .= '#' . $this->hash;
        }

        return $link;
    }

    public function getTarget(): string
    {
        return $this->target;
    }

    public function setTarget(?string $target): void
    {
        $this->target = $target;
    }

    public function getHash(): ?string
    {
        return $this->hash;
    }

    public function setHash(?string $hash): void
    {
        $this->hash = $hash;
    }

    public function getIdentity(): IdentityInterface
    {
        return $this->identity;
    }

    public function setIdentity(IdentityInterface $identity): void
    {
        $this->identity = $identity;
    }

    public function getAttributes(): array
    {
        return $this->attributes;
    }

    public function setAttributes(array $attributes): void
    {
        $this->attributes = $attributes;
    }

    public function setAttribute(string $name, mixed $value): void
    {
        $this->attributes[$name] = $value;
    }

    public function getAttribute(string $name, mixed $default = null): mixed
    {
        return $this->attributes[$name] ?? $default;
    }
}
