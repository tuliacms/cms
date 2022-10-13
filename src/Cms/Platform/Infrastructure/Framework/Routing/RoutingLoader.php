<?php

declare(strict_types=1);

namespace Tulia\Cms\Platform\Infrastructure\Framework\Routing;

use Symfony\Component\Config\Loader\LoaderInterface;
use Symfony\Component\Config\Loader\LoaderResolverInterface;
use Symfony\Component\Routing\Route;
use Symfony\Component\Routing\RouteCollection;

/**
 * @author Adam Banaszkiewicz
 */
final class RoutingLoader implements LoaderInterface
{
    private array $websites = [
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

    public function __construct(
        private readonly LoaderInterface $symfonyLoader,
        private readonly string $environment,
    ) {
    }

    public function load(mixed $resource, string $type = null)
    {
        /** @var RouteCollection $collection */
        $collection = $this->symfonyLoader->load($resource, $type);

        foreach ($collection->all() as $name => $route) {
            if (strncmp($name, 'backend.', 8) === 0) {
                $collection->remove($name);
                $this->processRoute(true, $name, $route, $collection, $this->generateBackendPath(...));
            }
            if (strncmp($name, 'frontend.', 9) === 0) {
                $collection->remove($name);
                $this->processRoute(false, $name, $route, $collection, $this->generateFrontendPath(...));
            }
        }

        return $collection;
    }

    public function supports(mixed $resource, string $type = null)
    {
        return $this->symfonyLoader->supports($resource, $type);
    }

    public function getResolver()
    {
        return $this->symfonyLoader->getResolver();
    }

    public function setResolver(LoaderResolverInterface $resolver)
    {
        return $this->symfonyLoader->setResolver($resolver);
    }

    private function generateBackendPath(Route $route, array $website, array $locale): string
    {
        return $locale['path_prefix'].$website['backend_prefix'].$locale['locale_prefix'].$route->getPath();
    }

    private function generateFrontendPath(Route $route, array $website, array $locale): string
    {
        return $locale['path_prefix'].$locale['locale_prefix'].$route->getPath();
    }

    private function processRoute(
        bool $backend,
        string $name,
        Route $route,
        RouteCollection $collection,
        \Closure $pathGenerator
    ): void {
        foreach ($this->websites as $website) {
            foreach ($website['locales'] as $locale) {
                $newName = sprintf('%s.%s.%s', $website['id'], $locale['code'], $name);

                $path = $pathGenerator($route, $website, $locale);

                $defaults = $route->getDefaults();
                $defaults['_locale'] = $locale['code'];
                $defaults['_website'] = $website['id'];
                $defaults['_is_backend'] = $backend;

                $collection->add($newName, new Route(
                    path: $path,
                    defaults: $defaults,
                    requirements: $route->getRequirements(),
                    options: $route->getOptions(),
                    host: $this->environment === 'prod'
                              ? $locale['domain']
                              : $locale['domain_development'],
                    schemes: $route->getSchemes(),
                    methods: $route->getMethods(),
                    condition: $route->getCondition(),
                ));
            }
        }
    }
}
