<?php

declare(strict_types=1);

namespace Tulia\Component\Theme\Bridge\Twig\Loader;

use Tulia\Component\Templating\Twig\Loader\AdvancedFilesystemLoader;
use Tulia\Component\Theme\ManagerInterface;
use Twig\Loader\FilesystemLoader;
use Twig\Loader\LoaderInterface;
use Twig\Source;

/**
 * @author Adam Banaszkiewicz
 */
class NamespaceLoader implements LoaderInterface
{
    private ?FilesystemLoader $loader = null;

    public function __construct(
        private readonly ManagerInterface $manager,
        private readonly AdvancedFilesystemLoader $filesystemLoader,
    ) {
    }

    public function getSourceContext($name): Source
    {
        $this->resolveLoader();

        return $this->loader->getSourceContext($name);
    }

    public function getCacheKey($name): string
    {
        $this->resolveLoader();

        return $this->loader->getCacheKey($name);
    }

    public function isFresh($name, $time): bool
    {
        $this->resolveLoader();

        return $this->loader->isFresh($name, $time);
    }

    public function exists($name): bool
    {
        $this->resolveLoader();

        return $this->loader->exists($name);
    }

    /**
     * Makes this loader to be lazy.
     */
    private function resolveLoader(): void
    {
        if ($this->loader !== null) {
            return;
        }

        $this->loader = new FilesystemLoader;

        foreach ($this->manager->getThemes() as $theme) {
            if (is_dir($theme->getViewsDirectory())) {
                $this->loader->addPath($theme->getViewsDirectory(), $theme->getName());
                $this->filesystemLoader->setPath('@'.$theme->getName(), $theme->getViewsDirectory());
            }
        }

        $theme = $this->manager->getTheme();

        if (!is_dir($theme->getViewsDirectory())) {
            $theme = $this->manager->getStorage()->get($theme->getParent());
        }

        $this->loader->addPath($theme->getViewsDirectory(), 'theme');
    }
}
