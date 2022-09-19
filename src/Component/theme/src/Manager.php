<?php

declare(strict_types=1);

namespace Tulia\Component\Theme;

use Tulia\Component\Theme\Configuration\ConfigurationLoaderInterface;
use Tulia\Component\Theme\Storage\StorageInterface;
use Tulia\Component\Theme\Resolver\ResolverAggregateInterface;
use Tulia\Component\Theme\Loader\ThemeLoader\ThemeLoaderInterface;

/**
 * @author Adam Banaszkiewicz
 */
class Manager implements ManagerInterface
{
    private ?ThemeInterface $theme = null;

    public function __construct(
        private readonly StorageInterface $storage,
        private readonly ResolverAggregateInterface $resolver,
        private readonly ThemeLoaderInterface $loader,
        private readonly ConfigurationLoaderInterface $configurationLoader,
    ) {
    }

    public function getTheme(): ThemeInterface
    {
        if ($this->theme) {
            return $this->theme;
        }

        $this->theme = $this->loader->load();
        $this->theme->setConfig($this->configurationLoader->load($this->resolver, $this->theme));

        return $this->theme;
    }

    public function getThemes(): iterable
    {
        return $this->storage->all();
    }

    public function getStorage(): StorageInterface
    {
        return $this->storage;
    }

    public function getResolver(): ResolverAggregateInterface
    {
        return $this->resolver;
    }

    public function getLoader(): ThemeLoaderInterface
    {
        return $this->loader;
    }
}
