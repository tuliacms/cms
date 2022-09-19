<?php

declare(strict_types=1);

namespace Tulia\Cms\Theme\Infrastructure\Framework\Theme\Loader;

use Tulia\Component\Theme\Configuration\Configuration;
use Tulia\Component\Theme\Configuration\ConfigurationInterface;
use Tulia\Component\Theme\Resolver\ResolverAggregateInterface;
use Tulia\Component\Theme\ThemeInterface;

/**
 * @author Adam Banaszkiewicz
 */
final class LazyConfiguration implements ConfigurationInterface
{
    private bool $resolved = false;

    public function __construct(
        private readonly ResolverAggregateInterface $resolver,
        private readonly ThemeInterface $theme,
        private readonly string $websiteId,
        private readonly string $locale,
    ) {
    }

    public function add(string $group, string $code, mixed $value = null): void
    {
        $this->resolve();
        $this->config->add($group, $code, $value);
    }

    public function all(?string $group = null): array
    {
        $this->resolve();
        return $this->config->all($group);
    }

    public function get(string $group, string $code = '', mixed $default = null): mixed
    {
        $this->resolve();
        return $this->config->get($group, $code, $default);
    }

    public function remove(string $group, ?string $code = null): void
    {
        $this->resolve();
        $this->config->remove($group, $code);
    }

    public function has(string $group, ?string $code = null, ?string $valueKey = null): bool
    {
        $this->resolve();
        return $this->config->has($group, $code, $valueKey);
    }

    public function getRegisteredWidgetSpaces(): array
    {
        $this->resolve();
        return $this->config->getRegisteredWidgetSpaces();
    }

    public function getRegisteredWidgetStyles(): array
    {
        $this->resolve();
        return $this->config->getRegisteredWidgetStyles();
    }

    public function merge(ConfigurationInterface $configuration): void
    {
        $this->resolve();
        $this->config->merge($configuration);
    }

    private function resolve(): void
    {
        if ($this->resolved) {
            return;
        }

        $this->config = new Configuration();
        $this->resolver->resolve($this->config, $this->theme, $this->websiteId, $this->locale);
        $this->resolved = true;
    }
}
