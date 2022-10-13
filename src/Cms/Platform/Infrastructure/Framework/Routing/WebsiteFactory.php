<?php

declare(strict_types=1);

namespace Tulia\Cms\Platform\Infrastructure\Framework\Routing;

use Symfony\Component\HttpFoundation\RequestStack;
use Tulia\Component\Routing\Enum\SslModeEnum;
use Tulia\Component\Routing\Website\Locale\Locale;
use Tulia\Component\Routing\Website\Website;
use Tulia\Component\Routing\Website\WebsiteInterface;
use Tulia\Component\Routing\Website\WebsiteMatcher;
use Tulia\Component\Routing\Website\WebsiteRegistryInterface;

/**
 * @author Adam Banaszkiewicz
 */
final class WebsiteFactory
{
    private static array $websites = [
        [
            'id' => 'ba43b191-1509-443a-a7d8-6e7d88c449fe',
            'name' => 'Demo Tulia CMS',
            'active' => true,
            'backend_prefix' => '/administrator',
            'locales' => [
                [
                    'code' => 'en_US',
                    'domain' => 'tulia.com',
                    'domain_development' => 'tulia.loc',
                    'locale_prefix' => NULL,
                    'path_prefix' => NULL,
                    'ssl_mode' => 'ALLOWED_BOTH',
                    'is_default' => true,
                ],
                [
                    'code' => 'pl_PL',
                    'domain' => 'tulia.com',
                    'domain_development' => 'tulia.loc',
                    'locale_prefix' => '/pl',
                    'path_prefix' => NULL,
                    'ssl_mode' => 'ALLOWED_BOTH',
                    'is_default' => false,
                ],
            ],
        ],
        [
            'id' => '4135f6ba-41d4-4dd7-8420-c41c7b0dc24b',
            'name' => 'B2B Tulia CMS',
            'active' => true,
            'backend_prefix' => '/administrator',
            'locales' => [
                [
                    'code' => 'pl_PL',
                    'domain' => 'b2b.tulia.com',
                    'domain_development' => 'tulia.loc',
                    'locale_prefix' => NULL,
                    'path_prefix' => '/b2b',
                    'ssl_mode' => 'ALLOWED_BOTH',
                    'is_default' => true,
                ],
            ],
        ],
    ];

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

    public static function factory(
        RequestStack $requestStack,
        WebsiteRegistryInterface $websiteRegistry,
    ): WebsiteInterface {
        $request = $requestStack->getMainRequest();

        $sourceWebsite = WebsiteMatcher::matchAgainstRequest(
            self::flattenWebsites(self::$websites, true),
            $request->getHttpHost(),
            $request->getPathInfo()
        );

        $website = $websiteRegistry->get($sourceWebsite['id']);

        $locales = [];

        foreach ($website->getLocales() as $locale) {
            $locales[] = new Locale(
                code: $locale->getCode(),
                domain: $locale->getDomain(),
                localePrefix: $locale->getLocalePrefix(),
                pathPrefix: $locale->getPathPrefix(),
                sslMode: SslModeEnum::tryFrom($locale->getSslMode()),
            );
        }

        return new Website(
            id: $website->getId(),
            name: $website->getName(),
            backendPrefix: $website->getBackendPrefix(),
            isBackend: $sourceWebsite['is_backend'],
            basepath: $sourceWebsite['basepath'],
            locales: $locales,
            defaultLocale: $website->getDefaultLocale()->getCode(),
            activeLocale: $sourceWebsite['locale_code'],
            active: $website->isActive(),
        );
    }
}
