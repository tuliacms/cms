<?php

declare(strict_types=1);

namespace Tulia\Cms\Theme\Infrastructure\Framework\Theme\Loader;

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
        private readonly string $configFilename,
        private readonly WebsiteInterface $website,
    ) {
    }

    public function load(): ThemeInterface
    {
        $config = include $this->configFilename;
        $websiteId = $this->website->getId();

        if (isset($config['cms.theme'][$websiteId]) && $this->storage->has($config['cms.theme'][$websiteId])) {
            return $this->storage->get($config['cms.theme'][$websiteId]);
        }

        return new DefaultTheme();
    }
}
