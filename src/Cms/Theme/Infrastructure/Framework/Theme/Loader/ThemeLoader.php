<?php

declare(strict_types=1);

namespace Tulia\Cms\Theme\Infrastructure\Framework\Theme\Loader;

use Tulia\Cms\Platform\Domain\Service\DynamicConfigurationInterface;
use Tulia\Component\Routing\Website\WebsiteInterface;
use Tulia\Component\Theme\Storage\StorageInterface;
use Tulia\Component\Theme\ThemeInterface;
use Tulia\Component\Theme\Loader\ThemeLoader\ThemeLoaderInterface;
use Tulia\Cms\Platform\Infrastructure\DefaultTheme\DefaultTheme;

/**
 * @author Adam Banaszkiewicz
 */
class ThemeLoader implements ThemeLoaderInterface
{
    public function __construct(
        private readonly StorageInterface $storage,
        private readonly DynamicConfigurationInterface $configuration,
        private readonly WebsiteInterface $website,
    ) {
    }

    public function load(): ThemeInterface
    {
        $config = $this->configuration->get('cms.theme');
        $websiteId = $this->website->getId();

        if (isset($config[$websiteId]) && $this->storage->has($config[$websiteId])) {
            return $this->storage->get($config[$websiteId]);
        }

        return new DefaultTheme();
    }
}
