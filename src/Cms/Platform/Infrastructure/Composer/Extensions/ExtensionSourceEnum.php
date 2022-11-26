<?php

declare(strict_types=1);

namespace Tulia\Cms\Platform\Infrastructure\Composer\Extensions;

/**
 * @author Adam Banaszkiewicz
 */
enum ExtensionSourceEnum: string
{
    case LOCAL = 'local';
    case VENDOR = 'vendor';
}
