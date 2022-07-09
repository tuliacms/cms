<?php

declare(strict_types=1);

namespace Tulia\Cms\Theme\Infrastructure\Framework\Theme\Loader;

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
        private StorageInterface $storage,
        private string $configFilename
    ) {
    }

    public function load(): ThemeInterface
    {
        $config = include $this->configFilename;

        if (isset($config['cms.theme']) && $this->storage->has($config['cms.theme'])) {
            return $this->storage->get($config['cms.theme']);
        }

        return new DefaultTheme();
    }
}
