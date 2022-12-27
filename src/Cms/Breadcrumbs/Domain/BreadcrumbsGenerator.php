<?php

declare(strict_types=1);

namespace Tulia\Cms\Breadcrumbs\Domain;

use Symfony\Component\HttpFoundation\Request;
use Tulia\Cms\Platform\Shared\Breadcrumbs\Breadcrumbs;
use Tulia\Cms\Platform\Shared\Breadcrumbs\BreadcrumbsInterface;

/**
 * @author Adam Banaszkiewicz
 */
class BreadcrumbsGenerator implements BreadcrumbsGeneratorInterface
{
    public function __construct(
        private readonly BreadcrumbsResolverRegistryInterface $registry,
    ) {
    }

    public function generateFromRequest(Request $request): BreadcrumbsInterface
    {
        $identity = null;

        foreach ($this->registry->all() as $resolver) {
            if ($identity = $resolver->findRootIdentity($request)) {
                break;
            }
        }

        if (!$identity) {
            return new Breadcrumbs();
        }

        return $this->generateFromIdentity($identity, $request->attributes->get('_website'), $request->attributes->get('_locale'));
    }

    private function generateFromIdentity(string $identity, string $websiteId, string $locale): BreadcrumbsInterface
    {
        $breadcrumbs = new Breadcrumbs();
        $homepageAdded = $identity === 'frontend.homepage';
        $securityLooper = 20;

        do {
            $parent = null;
            $resolverCalled = false;

            foreach ($this->registry->all() as $resolver) {
                if ($resolver->supports($identity) === false) {
                    continue;
                }

                $parent = $resolver->fillBreadcrumbs($identity, $websiteId, $locale, $breadcrumbs);
                $resolverCalled = true;
            }

            if ($parent === null && $homepageAdded === false) {
                $parent = 'frontend.homepage';
                $homepageAdded = true;
            }

            $identity = $parent;
            $securityLooper--;
        } while ($parent && $resolverCalled && $securityLooper);

        return $breadcrumbs;
    }
}
