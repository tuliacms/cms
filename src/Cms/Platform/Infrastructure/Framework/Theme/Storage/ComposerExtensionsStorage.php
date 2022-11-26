<?php

declare(strict_types=1);

namespace Tulia\Cms\Platform\Infrastructure\Framework\Theme\Storage;

use Psr\Log\LoggerInterface;
use Tulia\Component\Theme\Exception\MissingThemeException;
use Tulia\Component\Theme\Storage\StorageInterface;
use Tulia\Component\Theme\ThemeInterface;

/**
 * @author Adam Banaszkiewicz
 */
final class ComposerExtensionsStorage implements StorageInterface
{
    private array $themes = [];

    public function __construct(
        private readonly array $themesInfo,
        private readonly string $rootDir,
        private readonly LoggerInterface $appLogger,
    ) {
    }

    public function all(): iterable
    {
        $this->resolveThemes();

        return $this->themes;
    }

    public function add(ThemeInterface $theme): void
    {
        $this->resolveThemes();

        $this->themes[$theme->getName()] = $theme;
    }

    public function get(string $name): ThemeInterface
    {
        $this->resolveThemes();

        if (!isset($this->themes[$name])) {
            throw new MissingThemeException(sprintf('Theme %s not found in storage.', $name));
        }

        return $this->themes[$name];
    }

    public function has(?string $name): bool
    {
        if (!$name) {
            return false;
        }

        $this->resolveThemes();

        return isset($this->themes[$name]);
    }

    public function getIterator(): \Traversable
    {
        $this->resolveThemes();

        return new \ArrayIterator($this->themes);
    }

    private function resolveThemes(): void
    {
        foreach ($this->themesInfo as $info) {
            $this->themes[$info['name']] = new $info['entrypoint'](
                name: $info['name'],
                source: $info['source'],
                manifest: $this->rootDir.'/'.$info['manifest'],
            );
        }
    }
}
