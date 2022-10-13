<?php

declare(strict_types=1);

namespace Tulia\Cms\Security\Framework\Security\RequestMatcher;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestMatcherInterface;
use Tulia\Cms\Platform\Infrastructure\Framework\Routing\Website\WebsiteInterface;

/**
 * @author Adam Banaszkiewicz
 */
class BackendRequestMatcher implements RequestMatcherInterface
{
    public function __construct(
        private readonly WebsiteInterface $website,
    ) {
    }

    public function matches(Request $request): bool
    {
        return $this->website->isBackend();
    }
}
