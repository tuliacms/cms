<?php

declare(strict_types=1);

namespace Tulia\Cms\Platform\Infrastructure\Framework\Routing\Website;

use Symfony\Component\HttpFoundation\RequestStack;
use Tulia\Cms\Platform\Infrastructure\Framework\Console\ConsoleWebsiteProvider;
use Tulia\Cms\Platform\Infrastructure\Framework\Routing\NotFoundViewRenderer;
use Tulia\Cms\Website\Domain\ReadModel\Finder\WebsiteFinderInterface;

/**
 * @author Adam Banaszkiewicz
 */
final class WebsiteFactory
{
    public static function factory(
        RequestStack $requestStack,
        WebsiteFinderInterface $websiteFinder,
        NotFoundViewRenderer $render,
        string $environment,
    ): WebsiteInterface {
        if (php_sapi_name() === 'cli') {
            [$websiteId, $activeLocale] = ConsoleWebsiteProvider::provideWebsite();
            $isBackend = false;
        } else {
            $request = $requestStack->getMainRequest();
            $websites = self::flattenWebsites($websiteFinder->all(), $environment === 'dev');

            $sourceWebsite = WebsiteMatcher::matchAgainstRequest(
                $websites,
                $request->getHttpHost(),
                $request->getPathInfo()
            );

            if (!$sourceWebsite) {
                $render->render();
            }

            $websiteId = $sourceWebsite['id'];
            $activeLocale = $sourceWebsite['locale_code'];
            $isBackend = $sourceWebsite['is_backend'];
        }

        $website = $websiteFinder->get($websiteId);
        $locales = [];

        foreach ($website->getLocales() as $locale) {
            $locales[] = new Locale(
                code: $locale->getCode(),
                domain: $environment === 'dev'
                    ? $locale->getDomainDevelopment()
                    : $locale->getDomain(),
                localePrefix: $locale->getLocalePrefix(),
                pathPrefix: $locale->getPathPrefix(),
                sslMode: SslModeEnum::tryFrom($locale->getSslMode()),
                active: $locale->isActive(),
            );
        }

        $website = new Website(
            id: $website->getId(),
            name: $website->getName(),
            backendPrefix: $website->getBackendPrefix(),
            isBackend: $isBackend,
            locales: $locales,
            defaultLocale: $website->getDefaultLocale()->getCode(),
            activeLocale: $activeLocale,
            active: $website->isActive(),
        );

        return $website;
    }

    /**
     * @param \Tulia\Cms\Website\Domain\ReadModel\Model\Website[] $websites
     */
    private static function flattenWebsites(array $websites, bool $developmentEnvironment): array
    {
        $result = [];

        foreach ($websites as $website) {
            foreach ($website->getLocales() as $locale) {
                $result[] = [
                    'id' => $website->getId(),
                    'active' => $website->isActive(),
                    'name' => $website->getName(),
                    'backend_prefix' => $website->getBackendPrefix(),
                    'domain' => $developmentEnvironment
                        ? $locale->getDomainDevelopment()
                        : $locale->getDomain(),
                    'path_prefix' => (string) $locale->getPathPrefix(),
                    'locale_prefix' => (string) $locale->getLocalePrefix(),
                    'locale_code' => $locale->getCode(),
                    'is_default' => $locale->isDefault(),
                    'ssl_mode' => $locale->getSslMode(),
                    // Both must be active to assume request is available
                    'is_active' => $website->isActive() && $locale->isActive(),
                ];
            }
        }

        return $result;
    }
}
