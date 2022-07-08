<?php

declare(strict_types=1);

namespace Tulia\Component\Routing;

use Psr\Log\LoggerInterface;
use Symfony\Component\Routing\RouteCollection;
use Symfony\Component\Routing\RouterInterface;

/**
 * @author Adam Banaszkiewicz
 */
class ChainRouter implements ChainRouterInterface
{
    private array $routers = [];
    private array $sortedRouters = [];
    private RouteCollection $routeCollection;
    protected ?LoggerInterface $logger = null;

    public function __construct(LoggerInterface $logger = null)
    {
        $this->logger = $logger;
        $this->routeCollection = new RouteCollection();
    }

    public function add(RouterInterface $router, int $priority = 0): void
    {
        if (empty($this->routers[$priority])) {
            $this->routers[$priority] = [];
        }

        $this->routers[$priority][] = $router;
        $this->sortedRouters = [];
    }

    public function all(): array
    {
        if (0 === count($this->sortedRouters)) {
            $this->sortedRouters = $this->sortRouters();
        }

        return $this->sortedRouters;
    }

    protected function sortRouters(): array
    {
        if ($this->routers === []) {
            return [];
        }

        krsort($this->routers);

        return array_merge(...$this->routers);
    }
}
