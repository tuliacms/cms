<?php

declare(strict_types=1);

namespace Tulia\Cms\Platform\Infrastructure\Framework\Routing;

use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\Routing\Matcher\RequestMatcherInterface;

/**
 * @author Adam Banaszkiewicz
 */
interface ChainRouterInterface
{
    public function add(RouterInterface $router, int $priority = 0): void;

    /**
     * @return RouterInterface[]|RequestMatcherInterface[]
     */
    public function all();
}
