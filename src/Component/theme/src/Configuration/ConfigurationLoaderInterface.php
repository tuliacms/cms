<?php

declare(strict_types=1);

namespace Tulia\Component\Theme\Configuration;

use Tulia\Component\Theme\Resolver\ResolverAggregateInterface;
use Tulia\Component\Theme\ThemeInterface;

/**
 * @author Adam Banaszkiewicz
 */
interface ConfigurationLoaderInterface
{
    public function load(ResolverAggregateInterface $resolver, ThemeInterface $theme): ConfigurationInterface;
}
