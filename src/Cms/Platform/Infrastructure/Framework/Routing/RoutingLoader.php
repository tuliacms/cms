<?php

declare(strict_types=1);

namespace Tulia\Cms\Platform\Infrastructure\Framework\Routing;

use Symfony\Component\Config\Loader\LoaderInterface;
use Symfony\Component\Config\Loader\LoaderResolverInterface;
use Symfony\Component\Routing\RouteCollection;
use Tulia\Cms\Platform\Infrastructure\Framework\Routing\Website\WebsiteInterface;

/**
 * @author Adam Banaszkiewicz
 */
final class RoutingLoader implements LoaderInterface
{
    public function __construct(
        private readonly LoaderInterface $symfonyLoader,
        private readonly WebsiteInterface $website,
    ) {
    }

    public function load(mixed $resource, string $type = null): mixed
    {
        /** @var RouteCollection $collection */
        $collection = $this->symfonyLoader->load($resource, $type);

        foreach ($collection->all() as $name => $route) {
            if (strncmp($name, 'backend.', 8) === 0) {
                $route->setPath('/administrator'.$route->getPath());
            }
        }

        return $collection;
    }

    public function supports(mixed $resource, string $type = null): bool
    {
        return $this->symfonyLoader->supports($resource, $type);
    }

    public function getResolver(): LoaderResolverInterface
    {
        return $this->symfonyLoader->getResolver();
    }

    public function setResolver(LoaderResolverInterface $resolver): void
    {
        $this->symfonyLoader->setResolver($resolver);
    }
}
