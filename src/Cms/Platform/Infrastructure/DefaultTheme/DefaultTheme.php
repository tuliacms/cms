<?php

declare(strict_types=1);

namespace Tulia\Cms\Platform\Infrastructure\DefaultTheme;

use Tulia\Cms\Platform\Version;
use Tulia\Component\Theme\AbstractTheme;

/**
 * @author Adam Banaszkiewicz
 */
class DefaultTheme extends AbstractTheme
{
    public function getDirectory(): string
    {
        return __DIR__;
    }
}
