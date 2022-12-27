<?php

declare(strict_types=1);

namespace Tulia\Cms\Content\Type\Infrastructure\Framework\Routing;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Exception\ResourceNotFoundException;
use Symfony\Component\Routing\Matcher\RequestMatcherInterface;
use Symfony\Component\Routing\RequestContext;
use Symfony\Component\Routing\RouteCollection;
use Symfony\Component\Routing\RouterInterface;
use Tulia\Cms\Content\Type\Domain\WriteModel\Routing\Router;
use Tulia\Cms\Platform\Infrastructure\Framework\Routing\FrontendRouteSuffixResolver;

/**
 * @author Adam Banaszkiewicz
 */
final class ContentTypeRouter implements RouterInterface, RequestMatcherInterface
{
    private ?RequestContext $context = null;

    public function __construct(
        private readonly FrontendRouteSuffixResolver $frontendRouteSuffixResolver,
        private readonly Router $contentTypeRouter,
    ) {
    }

    public function setContext(RequestContext $context): void
    {
        $this->context = $context;
    }

    public function getContext(): RequestContext
    {
        return $this->context;
    }

    public function getRouteCollection(): RouteCollection
    {
        // Dynamic routing don't have any static collection
        return new RouteCollection();
    }

    public function matchRequest(Request $request): array
    {
        $pathinfo = urldecode($request->getPathInfo());
        $pathinfo = $this->frontendRouteSuffixResolver->removeSuffix($pathinfo);

        $parameters = $this->contentTypeRouter->match($pathinfo, [
            '_locale' => $request->attributes->get('_locale'),
            '_website' => $request->attributes->get('_website'),
        ]);

        if ($parameters === []) {
            throw new ResourceNotFoundException('Node not found with given path.');
        }

        return $parameters;
    }

    public function generate(string $name, array $parameters = [], int $referenceType = self::ABSOLUTE_PATH): string
    {
        preg_match('#^frontend\.(?<type>[a-z0-9_]+)\.(?<code>[a-z0-9_]+)\.(?<id>[0-9a-fA-F]{8}\b-[0-9a-fA-F]{4}\b-[0-9a-fA-F]{4}\b-[0-9a-fA-F]{4}\b-[0-9a-fA-F]{12})$#', $name, $matches);

        if (!isset($matches['type'], $matches['code'], $matches['id'])) {
            return '';
        }

        $parameters['_locale'] = $this->context->getParameter('_locale');
        $parameters['_website'] = $this->context->getParameter('_website');

        $path = $this->contentTypeRouter->generate($matches['code'], $matches['id'], $parameters);

        return $this->frontendRouteSuffixResolver->appendSuffix($path);
    }

    public function match(string $pathinfo): array
    {
        throw new \RuntimeException('Tulia CMS do not supports UrlMatcherInterface::match()');
    }
}
