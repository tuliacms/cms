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
        StorageInterface $storage,
        string $configFilename
    ) {
        $this->storage = $storage;
        $this->configFilename = $configFilename;
    }

    public function load(): ThemeInterface
    {
        $themesByWebsite = include $this->configFilename;

        if (
            isset($themesByWebsite['default'])
            && $this->storage->has($themesByWebsite['default'])
        ) {
            return $this->storage->get($themesByWebsite['default']);
        }

        return new DefaultTheme();
    }
}
