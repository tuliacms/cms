<?php

declare(strict_types=1);

namespace Tulia\Component\Theme\Assetter;

use Requtize\Assetter\AssetterInterface;
use Requtize\Assetter\Exception\MissingAssetException;
use Tulia\Component\Theme\ManagerInterface;

/**
 * @author Adam Banaszkiewicz
 */
class ThemeConfigurationAssetsLoader
{
    public function __construct(
        private readonly ManagerInterface $manager,
        private readonly AssetterInterface $assetter,
    ) {
    }

    /**
     * @throws MissingAssetException
     */
    public function load(): void
    {
        $theme = $this->manager->getTheme();

        $this->assetter->require($theme->getConfig()->getAssets());
    }
}
