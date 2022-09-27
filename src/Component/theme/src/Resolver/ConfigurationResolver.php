<?php

declare(strict_types=1);

namespace Tulia\Component\Theme\Resolver;

use Tulia\Component\Theme\Configuration\ConfigurationInterface;
use Tulia\Component\Theme\Configuration\ConfigurationRegistry;
use Tulia\Component\Theme\Customizer\DetectorInterface;
use Tulia\Component\Theme\ThemeInterface;

/**
 * @author Adam Banaszkiewicz
 */
class ConfigurationResolver implements ResolverInterface
{
    public function __construct(
        private readonly DetectorInterface $detector,
        private readonly ConfigurationRegistry $configurationRegistry,
    ) {
    }

    public function resolve(ConfigurationInterface $configuration, ThemeInterface $theme, string $websiteId, string $locale): void
    {
        if ($theme->hasParent()) {
            $this->configure($configuration, $theme->getParent());
        }

        $this->configure($configuration, $theme);
    }

    private function configure(ConfigurationInterface $configuration, ThemeInterface $theme): void
    {
        $configuration->merge($this->configurationRegistry->get($theme->getName(), 'base'));

        if ($this->detector->isCustomizerMode()) {
            $configuration->merge($this->configurationRegistry->get($theme->getName(), 'customizer'));
        }
    }
}
