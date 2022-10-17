<?php

declare(strict_types=1);

namespace Tulia\Component\Theme\Bridge\Twig\Loader;

use Twig\Error\LoaderError;
use Twig\Loader\LoaderInterface;
use Twig\Source;

/**
 * @author Adam Banaszkiewicz
 */
final class ThemesAbsoluteFilepathLoader implements LoaderInterface
{
    private array $cache = [];

    public function __construct(
        private readonly string $themesPath,
    ) {
    }

    public function getSourceContext(string $name): Source
    {
        return new Source(
            file_get_contents($this->findTemplate($name)),
            $name,
            $name,
        );
    }

    public function getCacheKey(string $name): string
    {
        return $this->findTemplate($name);
    }

    public function isFresh(string $name, int $time): bool
    {
        return filemtime($this->findTemplate($name)) < $time;
    }

    public function exists(string $name): bool
    {
        return !!$this->findTemplate($name, false);
    }

    protected function findTemplate(string $path, bool $throwOnError = true): string
    {
        if (is_file($path)) {
            if (isset($this->cache[$path])) {
                return $this->cache[$path];
            }

            if (strpos($path, $this->themesPath) === 0) {
                return $this->cache[$path] = $path;
            }
        }

        if ($throwOnError) {
            throw new LoaderError(sprintf('Unable to find template "%s".', $path));
        }

        return '';
    }
}
