<?php

declare(strict_types=1);

namespace Tulia\Component\Theme\Resolver;

use Tulia\Component\Theme\Configuration\ConfigurationInterface;
use Tulia\Component\Theme\ThemeInterface;

/**
 * @author Adam Banaszkiewicz
 */
interface ResolverInterface
{
    public function resolve(ConfigurationInterface $configuration, ThemeInterface $theme): void;
}
