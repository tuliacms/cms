<?php

declare(strict_types=1);

namespace Tulia\Cms\Platform\Infrastructure\Framework\Console;

use Tulia\Component\Routing\Website\WebsiteInterface;
use Tulia\Component\Routing\Website\WebsiteProvider;

/**
 * @author Adam Banaszkiewicz
 */
final class ConsoleWebsiteProvider
{
    public static function provide(bool $developmentEnvironment): WebsiteInterface
    {
        $configFilename = __TULIA_PROJECT_DIR.'/config/dynamic.php';
        assert(file_exists($configFilename), 'Tulia CMS seems to be not installed. Please call make setup do initialize system.');

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

        return WebsiteProvider::provideDirectly($configFilename, $website, $locale, $developmentEnvironment);
    }
}
