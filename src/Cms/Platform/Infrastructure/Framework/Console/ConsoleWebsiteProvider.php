<?php

declare(strict_types=1);

namespace Tulia\Cms\Platform\Infrastructure\Framework\Console;

/**
 * @author Adam Banaszkiewicz
 */
final class ConsoleWebsiteProvider
{
    public static function provideWebsite(): array
    {
        $locale = null;
        $website = null;

        foreach ($_SERVER['argv'] as $key => $arg) {
            if (strncmp($arg, '--locale=', 9) === 0) {
                [, $locale] = explode('=', $arg);

                unset($_SERVER['argv'][$key]);
            }
            if (strncmp($arg, '--website=', 10) === 0) {
                [, $website] = explode('=', $arg);

                unset($_SERVER['argv'][$key]);
            }
        }

        $_SERVER['argc'] = count($_SERVER['argv']);

        return [$website, $locale];
    }
}
