<?php

declare(strict_types=1);

namespace Tulia\Component\Routing\Bridge\Symfony;

use Psr\Log\LoggerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\CacheWarmer\WarmableInterface;
use Symfony\Component\Routing\Exception\MethodNotAllowedException;
use Symfony\Component\Routing\Exception\ResourceNotFoundException;
use Symfony\Component\Routing\Exception\RouteNotFoundException;
use Symfony\Component\Routing\Matcher\RequestMatcherInterface;
use Symfony\Component\Routing\RequestContext;
use Symfony\Component\Routing\RouteCollection;
use Symfony\Component\Routing\RouterInterface;
use Tulia\Cms\Platform\Infrastructure\Framework\Routing\ChainRouterInterface;
use Tulia\Component\Routing\WebsitePrefixesResolver;

/**
 * @author Adam Banaszkiewicz
 */
class SymfonyRouterDecorator implements RouterInterface, RequestMatcherInterface, WarmableInterface
{
    public function __construct(
        private RouterInterface $symfonyRouter,
        private ChainRouterInterface $chainRouter,
        private WebsitePrefixesResolver $websitePrefixesResolver,
        private ?LoggerInterface $logger = null,
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

    public function getRouteCollection(): RouteCollection
    {
        $collection = new RouteCollection();

        foreach ($this->routers() as $router) {
            $collection->addCollection($router->getRouteCollection());
        }

        return $collection;
    }

    public function generate(string $name, array $parameters = [], int $referenceType = self::ABSOLUTE_PATH): string
    {
        foreach ($this->routers() as $router) {
            try {
                $router->setContext($this->getContext());
                $path = $router->generate($name, $parameters, $referenceType);

                if ($path !== null) {
                    return $this->websitePrefixesResolver->appendWebsitePrefixes(
                        $name,
                        $path,
                        $parameters
                    );
                }
            } catch (RouteNotFoundException $e) {
                continue;
            }
        }

        throw new RouteNotFoundException(sprintf('None of the routers in the chain matched route named %s.', $name));
    }

    public function matchRequest(Request $request): array
    {
        $methodNotAllowedException = null;

        foreach ($this->routers() as $router) {
            try {
                if ($router instanceof RequestMatcherInterface) {
                    return $router->matchRequest($request);
                }
            } catch (ResourceNotFoundException $e) {
                $this->log('Router ' . \get_class($router) . ' was not able to match, message "' . $e->getMessage() . '"');
            } catch (MethodNotAllowedException $e) {
                $this->log('Router ' . \get_class($router) . ' throws MethodNotAllowedException with message "' . $e->getMessage() . '"');
                $methodNotAllowedException = $e;
            }
        }

        $info = $request
            ? "this request\n$request"
            : "url '{$request->getPathinfo()}'";

        throw $methodNotAllowedException ?: new ResourceNotFoundException("None of the routers in the chain matched $info", 0, $e);
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
