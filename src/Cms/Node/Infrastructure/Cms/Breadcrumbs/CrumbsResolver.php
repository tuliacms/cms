<?php

declare(strict_types=1);

namespace Tulia\Cms\Node\Infrastructure\Cms\Breadcrumbs;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\RouterInterface;
use Tulia\Cms\Breadcrumbs\Domain\BreadcrumbsResolverInterface;
use Tulia\Cms\Content\Type\Domain\ReadModel\Service\ContentTypeRegistryInterface;
use Tulia\Cms\Node\Domain\ReadModel\Finder\NodeFinderInterface;
use Tulia\Cms\Node\Domain\ReadModel\Finder\NodeFinderScopeEnum;
use Tulia\Cms\Node\Domain\ReadModel\Model\Node;
use Tulia\Cms\Platform\Shared\Breadcrumbs\BreadcrumbsInterface;

/**
 * @author Adam Banaszkiewicz
 */
class CrumbsResolver implements BreadcrumbsResolverInterface
{
    public function __construct(
        private readonly RouterInterface $router,
        private readonly ContentTypeRegistryInterface $contentTypeRegistry,
        private readonly NodeFinderInterface $nodeFinder,
    ) {
    }

    public function findRootIdentity(Request $request): ?string
    {
        $route = $request->attributes->get('_route');
        $node  = $request->attributes->get('node');

        if (!$route || !$node) {
            return null;
        }

        return $this->supports($route) && $node instanceof Node ? $route : null;
    }

    public function fillBreadcrumbs(string $identity, string $websiteId, string $locale, BreadcrumbsInterface $breadcrumbs): ?string
    {
        [, , $type, $id] = explode('.', $identity);

        if ($this->contentTypeRegistry->has($type)) {
            $type = $this->contentTypeRegistry->get($type);

            if ($type->isHierarchical()) {
                $this->resolveHierarchyCrumbs($id, $websiteId, $locale, $breadcrumbs);
            } else {
                return $this->resolveCrumb($id, $websiteId, $locale, $breadcrumbs);
            }
        }

        return null;
    }

    public function supports(string $identity): bool
    {
        return strncmp($identity, 'frontend.node.', 14) === 0;
    }

    private function resolveHierarchyCrumbs(?string $nodeId, string $websiteId, string $locale, BreadcrumbsInterface $breadcrumbs): void
    {
        $nodes = [];
        $securityLooper = 10;

        while ($nodeId && $securityLooper) {
            $node = $this->nodeFinder->findOne([
                'id' => $nodeId,
                'website_id' => $websiteId,
                'locale' => $locale,
            ], NodeFinderScopeEnum::BREADCRUMBS);

            if ($node) {
                $nodes[]  = $node;
                $nodeId = $node->getParentId();
            } else {
                $nodeId = null;
            }

            $securityLooper--;
        }

        /** @var Node $node */
        foreach ($nodes as $node) {
            $breadcrumbs->unshift(
                $this->generateRoute($node->getType(), $node->getId()),
                $node->getTitle()
            );
        }
    }

    private function resolveCrumb(
        string $id,
        string $websiteId,
        string $locale,
        BreadcrumbsInterface $breadcrumbs,
    ): ?string {
        $node = $this->nodeFinder->findOne([
            'id' => $id,
            'website_id' => $websiteId,
            'locale' => $locale,
        ], NodeFinderScopeEnum::BREADCRUMBS);

        if (!$node) {
            return null;
        }

        $breadcrumbs->unshift(
            $this->generateRoute($node->getType(), $node->getId()),
            $node->getTitle()
        );

        if ($node->getMainCategory()) {
            return sprintf('frontend.taxonomy.%s.%s', 'category', $node->getMainCategory());
        }

        return null;
    }

    private function generateRoute(string $type, string $id): string
    {
        return $this->router->generate(sprintf('frontend.node.%s.%s', $type, $id));
    }
}
