<?php

declare(strict_types=1);

namespace Tulia\Cms\Security\Framework\Security\Http;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Matcher\RequestMatcherInterface;
use Symfony\Component\Routing\RouterInterface;

/**
 * @author Adam Banaszkiewicz
 */
class HttpUtilsUrlMatcher implements RequestMatcherInterface
{
    private RouterInterface $router;

    public function __construct(RouterInterface $router)
    {
        $this->router = $router;
    }

    public function matchRequest(Request $request): array
    {
        return $this->router->matchRequest($request);
    }
}
