<?php

declare(strict_types=1);

namespace Tulia\Cms\Platform\Infrastructure\Framework\Routing;

use Symfony\Component\Config\Loader\LoaderInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\CacheWarmer\WarmableInterface;
use Symfony\Component\Routing\Matcher\RequestMatcherInterface;
use Symfony\Component\Routing\RequestContext;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Contracts\Service\ServiceSubscriberInterface;
use Tulia\Component\Routing\Website\WebsiteInterface;

/**
 * @author Adam Banaszkiewicz
 */
final class Router implements WarmableInterface, ServiceSubscriberInterface, RouterInterface, RequestMatcherInterface
{
    public function __construct(
        private readonly RouterInterface $symfonyRouter,
        private readonly WebsiteInterface $website,
        private readonly ChainRouterInterface $chainRouter,
    ) {
    }

    public function setContext(RequestContext $context)
    {
        $this->symfonyRouter->setContext($context);
    }

    public function getContext(): RequestContext
    {
        return $this->symfonyRouter->getContext();
    }

    public function matchRequest(Request $request): array
    {
        return $this->symfonyRouter->matchRequest($request);
    }

    public function getRouteCollection()
    {
        return $this->symfonyRouter->getRouteCollection();
    }

    public static function getSubscribedServices(): array
    {
        return [
            'routing.loader' => LoaderInterface::class,
        ];
    }

    public function generate(string $name, array $parameters = [], int $referenceType = self::ABSOLUTE_PATH): string
    {
        if (0 === strncmp($name, 'backend.', 8) || 0 === strncmp($name, 'frontend.', 9)) {
            $name = sprintf(
                '%s.%s.%s',
                $parameters['_website'] ?? $this->website->getId(),
                $parameters['_locale'] ?? $this->website->getLocale()->getCode(),
                $name
            );
        }

        $context = $this->getContext();
        $context->setParameter('_locale', $this->website->getLocale()->getCode());
        $context->setParameter('_website', $this->website->getId());

        foreach ($this->chainRouter->all() as $router) {
            $router->setContext($context);
            $url = $router->generate($name, $parameters, $referenceType);

            if (!empty($url)) {
                return $url;
            }
        }

        return $this->symfonyRouter->generate($name, $parameters, $referenceType);
    }

    public function match(string $pathinfo): array
    {
        return $this->symfonyRouter->match($pathinfo);
    }

    public function warmUp(string $cacheDir)
    {
        $this->symfonyRouter->warmUp($cacheDir);
    }
}
