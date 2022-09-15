<?php

declare(strict_types=1);

namespace Tulia\Component\Routing;

use Tulia\Component\Routing\Website\WebsiteInterface;

/**
 * @author Adam Banaszkiewicz
 */
class WebsitePrefixesResolver
{
    public function __construct(
        private readonly WebsiteInterface $website,
    ) {
    }

    public function appendWebsitePrefixes(string $name, string $uri, array $parameters = []): string
    {
        if (!isset($parameters['_locale'])) {
            $parameters['_locale'] = $this->website->getLocale()->getCode();
        }

        /**
         * Temporary fix to remove _locale parameter from homepage link.
         * @todo Find a better solution for that.
         */
        $uri = str_replace(['?_locale='.$parameters['_locale'], '&_locale='.$parameters['_locale']], '', $uri);

        /** @var array $parts */
        $parts = parse_url($uri);

        if (! isset($parts['path'])) {
            $parts['path'] = '/';
        }

        $localePrefix = $this->website->getLocaleByCode($parameters['_locale'])->getLocalePrefix();

        if (strncmp($name, 'backend.', 8) === 0) {
            if ($localePrefix !== $this->website->getDefaultLocale()->getPathPrefix()) {
                $parts['path'] = str_replace(
                    $this->website->getBackendPrefix(),
                    $this->website->getBackendPrefix() . $localePrefix,
                    $parts['path']
                );
            }

            $parts['path'] = $this->website->getLocale()->getPathPrefix() . $parts['path'];
        } else {
            $parts['path'] = $this->website->getLocale()->getPathPrefix() . $localePrefix . $parts['path'];
        }

        return
            (isset($parts['scheme']) ? $parts['scheme'] . '://' : '')
            .($parts['host'] ?? '')
            .($parts['path'] ?? '')
            .(isset($parts['query']) ? '?' . $parts['query'] : '')
        ;
    }
}
