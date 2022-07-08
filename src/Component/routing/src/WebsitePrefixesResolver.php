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
        private WebsiteInterface $website
    ) {
    }

    public function appendWebsitePrefixes(string $name, string $uri, array $parameters = []): string
    {
        /** @var array $parts */
        $parts = parse_url($uri);

        if (! isset($parts['path'])) {
            $parts['path'] = '/';
        }

        if (!isset($parameters['_locale'])) {
            $parameters['_locale'] = $this->website->getLocale()->getCode();
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
