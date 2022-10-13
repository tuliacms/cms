<?php

declare(strict_types=1);

namespace Tulia\Cms\Security\Framework\Security\Http;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Routing\Matcher\UrlMatcherInterface;
use Tulia\Cms\Platform\Infrastructure\Framework\Routing\Website\WebsiteInterface;

/**
 * @author Adam Banaszkiewicz
 */
final class HttpUtils extends \Symfony\Component\Security\Http\HttpUtils
{
    public function __construct(
        UrlGeneratorInterface $urlGenerator,
        UrlMatcherInterface $urlMatcher,
        private readonly WebsiteInterface $website,
    ) {
        parent::__construct($urlGenerator, $urlMatcher, null, null);
    }

    public function checkRequestPath(Request $request, string $path): bool
    {
        if ('/' !== $path[0]) {
            $path = sprintf('%s.%s.%s', $this->website->getId(), $this->website->getLocale()->getCode(), $path);
        }

        return parent::checkRequestPath($request, $path);
    }
}
