<?php

declare(strict_types=1);

namespace Tulia\Component\Theme;

use Tulia\Component\Theme\Configuration\ConfigurationInterface;
use Tulia\Component\Theme\Configuration\ConfigurationLoaderInterface;
use Tulia\Component\Theme\Resolver\ResolverAggregateInterface;

/**
 * @author Adam Banaszkiewicz
 */
abstract class AbstractTheme implements ThemeInterface
{
    protected $directory;
    protected $parent;
    protected $config;
    protected \Closure $parentThemeLoader;
    protected ?ThemeInterface $parentInstance = null;
    private array $manifestSource = [];

    public function __construct(
        protected string $name,
        protected string $source,
        protected string $manifest,
    ) {
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function hasParent(): bool
    {
        return !!$this->parent;
    }

    public function setParentThemeLoader(callable $loader): void
    {
        $this->parentThemeLoader = $loader;
    }

    public function getPreviewDirectory(): ?string
    {
        $directory = $this->getDirectory().'/Resources/preview';

        return is_dir($directory) ? $directory : null;
    }

    public function getDirectory(): string
    {
        if (! $this->directory) {
            $this->directory = $this->resolveDirectory();
        }

        return $this->directory;
    }

    public function getViewsDirectory(): string
    {
        return $this->getDirectory().'/Resources/views';
    }

    protected function resolveDirectory(): string
    {
        return realpath(\dirname((new \ReflectionClass($this))->getFileName()));
    }

    public function getParent(): self
    {
        if (! $this->hasParent()) {
            throw new \Exception(sprintf('Theme %s does not have a parent. Please check this with hasParent() method.', $this->name));
        }

        if (!$this->parentInstance) {
            $this->parentInstance = ($this->parentThemeLoader)($this->parent);
            $this->parentInstance->setConfigurationLoader($this->configurationLoader, $this->resolverAggregate);
        }

        return $this->parentInstance;
    }

    public function getParentName(): ?string
    {
        return $this->parent;
    }

    public function getConfig(): ConfigurationInterface
    {
        if (!$this->config) {
            $this->config = $this->configurationLoader->load($this->resolverAggregate, $this);
        }

        return $this->config;
    }

    public function getManifest(): array
    {
        if ([] !== $this->manifestSource) {
            return $this->manifestSource;
        }

        if (!is_file($this->manifest)) {
            return [];
        }

        return $this->manifestSource = json_decode(file_get_contents($this->manifest), true);
    }

    public function setConfigurationLoader(
        ConfigurationLoaderInterface $configurationLoader,
        ResolverAggregateInterface $resolverAggregate,
    ): void {
        $this->configurationLoader = $configurationLoader;
        $this->resolverAggregate = $resolverAggregate;
    }
}
