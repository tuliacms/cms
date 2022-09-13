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
class DirectoryDiscoveryStorage implements StorageInterface
{
    private array $themes = [];

    public function __construct(
        private readonly array $extensionsDirectories,
        private readonly LoggerInterface $appLogger
    ) {
    }

    /**
     * {@inheritdoc}
     */
    public function all(): iterable
    {
        $this->resolveThemes();

        return $this->themes;
    }

    /**
     * {@inheritdoc}
     */
    public function add(ThemeInterface $theme): void
    {
        $this->resolveThemes();

        $this->themes[$theme->getName()] = $theme;
    }

    /**
     * {@inheritdoc}
     */
    public function get(string $name): ThemeInterface
    {
        $this->resolveThemes();

        if (!isset($this->themes[$name])) {
            throw new MissingThemeException(sprintf('Theme %s not found in storage.', $name));
        }

        return $this->themes[$name];
    }

    /**
     * {@inheritdoc}
     */
    public function has(?string $name): bool
    {
        if (!$name) {
            return false;
        }

        $this->resolveThemes();

        return isset($this->themes[$name]);
    }

    /**
     * {@inheritdoc}
     */
    public function getIterator(): \Traversable
    {
        $this->resolveThemes();

        return new \ArrayIterator($this->themes);
    }

    private function resolveThemes(): void
    {
        foreach ($this->extensionsDirectories as $directory => $prefix) {
            if (!is_dir($directory)) {
                continue;
            }

            foreach (new \DirectoryIterator($directory) as $namespace) {
                if ($namespace->isDot() || !$namespace->isDir()) {
                    continue;
                }

                $themes = new \DirectoryIterator($directory . '/' . $namespace->getFilename());

                foreach ($themes as $theme) {
                    if ($theme->isDot()) {
                        continue;
                    }

                    $themeClassname = $prefix . '\\' . $namespace->getFilename() . '\\' .  $theme->getFilename() . '\\Theme';

                    /** @var ThemeInterface $themeObject */
                    $themeObject = new $themeClassname();

                    $this->themes[$themeObject->getName()] = $themeObject;
                }
            }
        }
    }
}
