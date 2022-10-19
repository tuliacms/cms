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
        $root = null;

        foreach ($this->registry->all() as $resolver) {
            if ($root = $resolver->findRootCrumb($request)) {
                break;
            }
        }

        if (!$root) {
            return new Breadcrumbs();
        }

        return $this->generateFromIdentity($root);
    }

    public function generateFromIdentity(Crumb $crumb): BreadcrumbsInterface
    {
        $breadcrumbs = new Breadcrumbs();
        $homepageAdded = $crumb->getCode() === 'frontend.homepage';
        $parent = null;
        $securityLooper = 10;

        do {
            $resolverCalled = false;

            foreach ($this->registry->all() as $resolver) {
                if ($resolver->supports($crumb) === false) {
                    continue;
                }

                $parent = $resolver->fillBreadcrumbs($crumb, $breadcrumbs);
                $resolverCalled = true;
            }

            if ($parent === null && $homepageAdded === false) {
                $parent = new Crumb('frontend.homepage', []);
                $homepageAdded = true;
            }

            $crumb = $parent;
            $securityLooper--;
        } while ($crumb && $resolverCalled && $securityLooper);

        return $breadcrumbs;
    }
}
