<?php

declare(strict_types=1);

namespace Tulia\Cms\Theme\Domain\WriteModel\Service;

/**
 * @author Adam Banaszkiewicz
 */
interface DefaultThemeConfigurationProviderInterface
{
    public function provideDefaultValues(string $theme): array;

    public function provideMultilingualControls(string $theme): array;
}
