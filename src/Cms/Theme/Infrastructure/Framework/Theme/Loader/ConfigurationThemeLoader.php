<?php

declare(strict_types=1);

namespace Tulia\Cms\Theme\Infrastructure\Framework\Theme\Loader;

use Tulia\Cms\Options\Domain\ReadModel\OptionsFinderInterface;
use Tulia\Cms\Platform\Infrastructure\Framework\Routing\Website\WebsiteInterface;
use Tulia\Component\Theme\Storage\StorageInterface;
use Tulia\Component\Theme\ThemeInterface;
use Tulia\Component\Theme\Loader\ThemeLoader\ThemeLoaderInterface;
use Tulia\Cms\Platform\Infrastructure\DefaultTheme\DefaultTheme;

/**
 * @author Adam Banaszkiewicz
 */
class ConfigurationThemeLoader implements ThemeLoaderInterface
{
    public function __construct(
        private readonly StorageInterface $storage,
        private readonly OptionsFinderInterface $finder,
        private readonly WebsiteInterface $website,
    ) {
    }

    public function getActiveTheme(): ThemeInterface
    {
        $theme = $this->finder->findByName('theme', $this->website->getId(), $this->website->getLocale()->getCode());

        if ($theme && $this->storage->has($theme)) {
            return $this->storage->get($theme);
        }

        return new DefaultTheme('DefaultTheme', 'local', '');
    }
}
