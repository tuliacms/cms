<?php

declare(strict_types=1);

namespace Tulia\Cms\Platform\Infrastructure\Framework\Routing;

use Psr\Log\LoggerInterface;
use Symfony\Component\Config\Loader\LoaderInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\CacheWarmer\WarmableInterface;
use Symfony\Component\Routing\Exception\MethodNotAllowedException;
use Symfony\Component\Routing\Exception\ResourceNotFoundException;
use Symfony\Component\Routing\Exception\RouteNotFoundException;
use Symfony\Component\Routing\Matcher\RequestMatcherInterface;
use Symfony\Component\Routing\RequestContext;
use Symfony\Component\Routing\RouteCollection;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Contracts\Service\ServiceSubscriberInterface;
use Tulia\Cms\Platform\Infrastructure\Framework\Routing\Website\WebsiteInterface;

/**
 * @author Adam Banaszkiewicz
 */
final class TuliaRouter implements WarmableInterface, ServiceSubscriberInterface, RouterInterface, RequestMatcherInterface
{
    public function __construct(
        private readonly RouterInterface $symfonyRouter,
        private readonly WebsiteInterface $website,
        private readonly ChainRouterInterface $chainRouter,
        private readonly ?LoggerInterface $logger = null,
    ) {
    }

    public function setContext(RequestContext $context): void
    {
        foreach ($this->routers() as $router) {
            $router->setContext($context);
        }
    }

    public function getContext(): RequestContext
    {
        return $this->symfonyRouter->getContext();
    }

    public function matchRequest(Request $request): array
    {
        $request->attributes->set('_website', $this->website->getId());
        $request->attributes->set('_locale', $this->website->getLocale()->getCode());

        $server = $request->server->all();
        $server['REQUEST_URI'] = $this->website->prepareRequestUriToRoutingMatching($server['REQUEST_URI']);

        $duplicate = $request->duplicate(server: $server);

        $methodNotAllowedException = null;

        foreach ($this->routers() as $router) {
            try {
                if ($router instanceof RequestMatcherInterface) {
                    return $router->matchRequest($duplicate);
                }
            } catch (ResourceNotFoundException $e) {
                $this->log('Router ' . \get_class($router) . ' was not able to match, message "' . $e->getMessage() . '"');
            } catch (MethodNotAllowedException $e) {
                $this->log('Router ' . \get_class($router) . ' throws MethodNotAllowedException with message "' . $e->getMessage() . '"');
                $methodNotAllowedException = $e;
            }
        }

        throw $methodNotAllowedException ?: new ResourceNotFoundException("None of the routers in the chain matched url '{$duplicate->getPathinfo()}' (original '{$request->getPathinfo()}')", 0, $e);
    }

    public function getRouteCollection(): RouteCollection
    {
        $collection = new RouteCollection();

        foreach ($this->routers() as $router) {
            $collection->addCollection($router->getRouteCollection());
        }

        return $collection;
    }

    public static function getSubscribedServices(): array
    {
        return [
            'routing.loader' => LoaderInterface::class,
        ];
    }

    public function generate(string $name, array $parameters = [], int $referenceType = self::ABSOLUTE_PATH): string
    {
        $context = $this->getContext();
        $context->setParameter('_locale', $this->website->getLocale()->getCode());
        $context->setParameter('_website', $this->website->getId());

        foreach ($this->routers() as $router) {
            try {
                $router->setContext($context);
                $path = $router->generate($name, $parameters, $referenceType);

                if ($path !== null) {
                    return $this->website->generateTargetPath($path, $parameters['_locale'] ?? $this->website->getLocale()->getCode());
                }
            } catch (RouteNotFoundException $e) {
                continue;
            }
        }

        throw new RouteNotFoundException(sprintf('None of the routers in the chain matched route named %s.', $name));
    }

    public function match(string $pathinfo): array
    {
        throw new \RuntimeException('Tulia CMS do not supports UrlMatcherInterface::match()');
    }

    public function warmUp(string $cacheDir): array
    {
        $result = [];

        foreach ($this->routers() as $router) {
            if ($router instanceof WarmableInterface) {
                $result[] = $router->warmUp($cacheDir);
            }
        }

        return array_merge(...$result);
    }

    /**
     * @return RouterInterface[]|RequestMatcherInterface[]
     */
    private function routers(): array
    {
        return array_merge([$this->symfonyRouter], $this->chainRouter->all());
    }

    private function log(string $message): void
    {
        if ($this->logger) {
            $this->logger->debug($message);
        }
    }
}
