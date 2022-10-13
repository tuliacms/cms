<?php

declare(strict_types=1);

namespace Tulia\Cms\Platform\Infrastructure\Framework\Routing;

use Symfony\Component\Config\Loader\LoaderInterface;
use Symfony\Component\Config\Loader\LoaderResolverInterface;
use Symfony\Component\Routing\Route;
use Symfony\Component\Routing\RouteCollection;
use Tulia\Cms\Website\Domain\ReadModel\Finder\WebsiteFinderInterface;
use Tulia\Cms\Website\Domain\ReadModel\Model\Locale;
use Tulia\Cms\Website\Domain\ReadModel\Model\Website;

/**
 * @author Adam Banaszkiewicz
 */
final class RoutingLoader implements LoaderInterface
{
    public function __construct(
        private readonly LoaderInterface $symfonyLoader,
        private readonly string $environment,
        private readonly WebsiteFinderInterface $websiteFinder,
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

    private function generateBackendPath(Route $route, Website $website, Locale $locale): string
    {
        return $locale->getPathPrefix().$website->getBackendPrefix().$locale->getLocalePrefix().$route->getPath();
    }

    private function generateFrontendPath(Route $route, Website $website, Locale $locale): string
    {
        return $locale->getPathPrefix().$locale->getLocalePrefix().$route->getPath();
    }

    private function processRoute(
        bool $backend,
        string $name,
        Route $route,
        RouteCollection $collection,
        \Closure $pathGenerator
    ): void {
        foreach ($this->websiteFinder->all() as $website) {
            foreach ($website->getLocales() as $locale) {
                $newName = sprintf('%s.%s.%s', $website->getId(), $locale->getCode(), $name);

                $path = $pathGenerator($route, $website, $locale);

                $defaults = $route->getDefaults();
                $defaults['_locale'] = $locale->getCode();
                $defaults['_website'] = $website->getId();
                $defaults['_is_backend'] = $backend;

                $collection->add($newName, new Route(
                    path: $path,
                    defaults: $defaults,
                    requirements: $route->getRequirements(),
                    options: $route->getOptions(),
                    host: $this->environment === 'prod'
                              ? $locale->getDomain()
                              : $locale->getDevelopmentDomain(),
                    schemes: $route->getSchemes(),
                    methods: $route->getMethods(),
                    condition: $route->getCondition(),
                ));
            }
        }
    }
}
