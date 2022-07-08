<?php

declare(strict_types=1);

namespace Tulia\Component\Routing\Website;

use Tulia\Component\Routing\Website\Locale\Locale;
use Tulia\Component\Routing\Website\Locale\LocaleInterface;

/**
 * @author Adam Banaszkiewicz
 */
final class WebsiteProvider
{
    public static function provide(
        string $configFilename,
        string $host,
        string $path,
        bool $developmentEnvironment
    ): WebsiteInterface {
        /** @var array $websiteData */
        $websiteData = include $configFilename;
        $websites = self::flattenWebsites($websiteData['cms.website'], $developmentEnvironment);

        $defaultWebsite = self::findDefaultWebsite($websites);
        $activeWebsite = WebsiteMatcher::matchAgainstRequest($websites, $host, $path,);

        return new Website(
            $activeWebsite['backend_prefix'],
            $activeWebsite['is_backend'],
            $activeWebsite['basepath'],
            self::collectLocales($websites),
            $defaultWebsite['locale_code'],
            $activeWebsite['locale_code']
        );
    }

    /**
     * @return LocaleInterface[]
     */
    private static function collectLocales(array $locales): array
    {
        return array_map(static function (array $locale) {
            return new Locale(
                $locale['locale_code'],
                $locale['domain'],
                $locale['locale_prefix'],
                $locale['path_prefix'],
                $locale['ssl_mode']
            );
        }, $locales);
    }

    private static function flattenWebsites(array $website, bool $developmentEnvironment): array
    {
        $result = [];

        foreach ($website['locales'] as $locale) {
            $result[] = [
                'backend_prefix' => $website['backend_prefix'],
                'domain' => (string) ($developmentEnvironment ? $locale['domain_development'] : $locale['domain']),
                'path_prefix' => (string) $locale['path_prefix'],
                'locale_prefix' => (string) $locale['locale_prefix'],
                'locale_code' => (string) $locale['locale_code'],
                'default' => (bool) $locale['default'],
                'ssl_mode' => (string) $locale['ssl_mode'],
            ];
        }

        return $result;
    }

    private static function findDefaultWebsite(array $locales): array
    {
        foreach ($locales as $locale) {
            if ($locale['default']) {
                return $locale;
            }
        }

        \assert(false, 'Configured website must have a default locale, which is not found in websites configuration.');
    }
}
