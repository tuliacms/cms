<?php

declare(strict_types=1);

namespace Tulia\Component\Theme\Storage;

use Tulia\Component\Theme\Exception\MissingThemeException;
use Tulia\Component\Theme\ThemeInterface;

/**
 * @author Adam Banaszkiewicz
 */
class ArrayStorage implements StorageInterface
{
    protected array $themes = [];

    public function __construct(iterable $themes = [])
    {
        foreach($themes as $theme) {
            $this->add($theme);
        }
    }

    public function all(): iterable
    {
        return $this->themes;
    }

    public function add(ThemeInterface $theme): void
    {
        $this->themes[$theme->getName()] = $theme;
    }

    public function get(string $name): ThemeInterface
    {
        if (!isset($this->themes[$name])) {
            throw new MissingThemeException(sprintf('Theme %s not found in storage.', $name));
        }

        return $this->themes[$name];
    }

    public function has(?string $name): bool
    {
        return isset($this->themes[$name]);
    }

    public function getIterator(): \Traversable
    {
        return new \ArrayIterator($this->themes);
    }
}
