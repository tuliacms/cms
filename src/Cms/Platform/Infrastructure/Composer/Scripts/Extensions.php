<?php

declare(strict_types=1);

namespace Tulia\Cms\Platform\Infrastructure\Composer\Scripts;

use Composer\Script\Event;

/**
 * @author Adam Banaszkiewicz
 */
final class Extensions
{
    public static function install(Event $event): void
    {
        $event->getComposer()->getPackage()->getExtra();
    }
}
