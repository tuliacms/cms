<?php

declare(strict_types=1);

namespace Tulia\Cms\Theme\Infrastructure\Framework\Theme\Loader;

use Tulia\Component\Routing\Website\WebsiteInterface;
use Tulia\Component\Theme\Configuration\Configuration;
use Tulia\Component\Theme\Configuration\ConfigurationInterface;
use Tulia\Component\Theme\Configuration\ConfigurationLoaderInterface;
use Tulia\Component\Theme\Resolver\ResolverAggregateInterface;
use Tulia\Component\Theme\ThemeInterface;

/**
 * @author Adam Banaszkiewicz
 */
final class ConfigurationLoader implements ConfigurationLoaderInterface
{
    public function __construct(
        private readonly WebsiteInterface $website,
    ) {
    }

    public function load(ResolverAggregateInterface $resolver, ThemeInterface $theme): ConfigurationInterface
    {
        $config = new Configuration();

        $resolver->resolve($config, $theme, $this->website->getId(), $this->website->getLocale()->getCode());

        return $config;
    }
}
