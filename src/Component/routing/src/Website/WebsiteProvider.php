<?php

declare(strict_types=1);

namespace Tulia\Component\Routing\Website;

use Tulia\Component\Routing\Enum\SslModeEnum;
use Tulia\Component\Routing\Website\Locale\Locale;
use Tulia\Component\Routing\Website\Locale\LocaleInterface;

/**
 * @author Adam Banaszkiewicz
 */
final class WebsiteProvider
{
    public static function provideByHostAndPath(
        string $configFilename,
        string $host,
        string $path,
        bool $developmentEnvironment
    ): WebsiteInterface {
        /** @var array $websiteData */
        $websiteData = include $configFilename;
        $websites = self::flattenWebsites($websiteData['cms.websites'], $developmentEnvironment);

        $currentWebsite = WebsiteMatcher::matchAgainstRequest($websites, $host, $path);

        if (!$currentWebsite) {
            self::show404($path, $host, $developmentEnvironment);
        }

        $defaultWebsite = self::findDefaultWebsite($websites, $currentWebsite['id']);

        return new Website(
            $currentWebsite['id'],
            $currentWebsite['name'],
            $currentWebsite['backend_prefix'],
            $currentWebsite['is_backend'],
            $currentWebsite['basepath'],
            self::collectLocales($websites, $currentWebsite['id']),
            $defaultWebsite['locale_code'],
            $currentWebsite['locale_code'],
            $currentWebsite['active']
        );
    }

    public static function provideDirectly(
        string $configFilename,
        ?string $website,
        ?string $locale,
        bool $developmentEnvironment
    ): WebsiteInterface {
        /** @var array $websiteData */
        $websiteData = include $configFilename;
        $websites = self::flattenWebsites($websiteData['cms.websites'], $developmentEnvironment);

        if (!$website) {
            $defaultWebsite = self::findDefaultWebsite($websites, $websites[0]['id']);
        } else {
            $defaultWebsite = self::findDefaultWebsite($websites, $website);
        }

        $currentWebsite = null;

        if ($locale === null) {
            $currentWebsite = $defaultWebsite;
        } else {
            foreach ($websites as $pretendent) {
                if ($pretendent['locale_code'] === $locale) {
                    $currentWebsite = $pretendent;
                }
            }
        }

        if (!$currentWebsite) {
            throw new \InvalidArgumentException(sprintf('Locale %s not defined in website.', $locale));
        }

        return new Website(
            $currentWebsite['id'],
            $currentWebsite['name'],
            $currentWebsite['backend_prefix'],
            false,
            $currentWebsite['path_prefix'] . $currentWebsite['locale_prefix'],
            self::collectLocales($websites, $currentWebsite['id']),
            $defaultWebsite['locale_code'],
            $currentWebsite['locale_code'],
            $currentWebsite['active']
        );
    }

    /**
     * @return LocaleInterface[]
     */
    private static function collectLocales(array $locales, string $websiteId): array
    {
        $result = [];

        foreach ($locales as $locale) {
            if ($locale['id'] === $websiteId) {
                $result[] = new Locale(
                    $locale['locale_code'],
                    $locale['domain'],
                    $locale['locale_prefix'],
                    $locale['path_prefix'],
                    SslModeEnum::from($locale['ssl_mode'])
                );
            }
        }

        return $result;
    }

    private static function flattenWebsites(array $websites, bool $developmentEnvironment): array
    {
        $result = [];

        foreach ($websites as $website) {
            foreach ($website['locales'] as $locale) {
                $result[] = [
                    'id' => $website['id'],
                    'active' => $website['active'],
                    'name' => $website['name'],
                    'backend_prefix' => $website['backend_prefix'],
                    'domain' => (string) (
                        $developmentEnvironment && $locale['domain_development']
                            ? $locale['domain_development']
                            : $locale['domain']
                    ),
                    'path_prefix' => (string) $locale['path_prefix'],
                    'locale_prefix' => (string) $locale['locale_prefix'],
                    'locale_code' => (string) $locale['code'],
                    'is_default' => (bool) $locale['is_default'],
                    'ssl_mode' => (string) $locale['ssl_mode'],
                ];
            }
        }

        return $result;
    }

    private static function findDefaultWebsite(array $locales, string $activeWebsiteId): array
    {
        foreach ($locales as $locale) {
            if ($locale['id'] === $activeWebsiteId && $locale['is_default']) {
                return $locale;
            }
        }

        \assert(false, 'Configured website must have a default locale, which is not found in websites configuration.');
    }

    private static function show404(string $path, string $host, bool $developmentEnvironment): void
    {
        header("HTTP/1.1 404 Not Found", true, 404);

        if ($developmentEnvironment) {
            include __DIR__.'/../Resources/view/404.dev.php';
        } else {
            include __DIR__.'/../Resources/view/404.prod.php';
        }

        exit;
    }
}
