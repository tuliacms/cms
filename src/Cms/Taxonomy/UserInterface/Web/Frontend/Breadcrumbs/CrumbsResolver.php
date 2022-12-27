<?php

declare(strict_types=1);

namespace Tulia\Cms\Taxonomy\UserInterface\Web\Frontend\Breadcrumbs;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\RouterInterface;
use Tulia\Cms\Breadcrumbs\Domain\BreadcrumbsResolverInterface;
use Tulia\Cms\Content\Type\Domain\ReadModel\Service\ContentTypeRegistryInterface;
use Tulia\Cms\Platform\Shared\Breadcrumbs\BreadcrumbsInterface;
use Tulia\Cms\Taxonomy\Domain\ReadModel\Finder\TermFinderInterface;
use Tulia\Cms\Taxonomy\Domain\ReadModel\Finder\TermFinderScopeEnum;
use Tulia\Cms\Taxonomy\Domain\ReadModel\Model\Term;

/**
 * @author Adam Banaszkiewicz
 */
class CrumbsResolver implements BreadcrumbsResolverInterface
{
    public function __construct(
        private readonly RouterInterface $router,
        private readonly TermFinderInterface $termFinder,
        private readonly ContentTypeRegistryInterface $contentTypeRegistry,
    ) {
    }

    public function findRootIdentity(Request $request): ?string
    {
        $route = $request->attributes->get('_route');
        $term  = $request->attributes->get('term');

        if (!$route || !$term) {
            return null;
        }

        return $this->supports($route) && $term instanceof Term ? $route : null;
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
        return strncmp($identity, 'frontend.taxonomy.', 18) === 0;
    }

    private function resolveHierarchyCrumbs(?string $termId, string $websiteId, string $locale, BreadcrumbsInterface $breadcrumbs): void
    {
        $terms = [];
        $securityLooper = 10;

        while ($termId && $securityLooper) {
            $term = $this->termFinder->findOne([
                'id' => $termId,
                'website_id' => $websiteId,
                'locale' => $locale,
            ], TermFinderScopeEnum::BREADCRUMBS);

            if ($term) {
                $terms[]  = $term;
                $termId = $term->getParentId();
            } else {
                $termId = null;
            }

            $securityLooper--;
        }

        /** @var Term $term */
        foreach ($terms as $term) {
            $breadcrumbs->unshift(
                $this->generateRoute($term->getType(), $term->getId()),
                $term->getName()
            );
        }
    }

    private function resolveCrumb(
        string $id,
        string $websiteId,
        string $locale,
        BreadcrumbsInterface $breadcrumbs,
    ): ?string {
        $term = $this->termFinder->findOne([
            'id' => $id,
            'website_id' => $websiteId,
            'locale' => $locale,
        ], TermFinderScopeEnum::BREADCRUMBS);

        if (!$term) {
            return null;
        }

        $breadcrumbs->unshift(
            $this->generateRoute($term->getType(), $term->getId()),
            $term->getName()
        );

        return null;
    }

    private function generateRoute(string $type, string $id): string
    {
        return $this->router->generate(sprintf('frontend.taxonomy.%s.%s', $type, $id));
    }
}
