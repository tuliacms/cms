<?php

declare(strict_types=1);

namespace Tulia\Cms\Homepage\UserInterface\Web\Frontend\Breadcrumbs;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Contracts\Translation\TranslatorInterface;
use Tulia\Cms\Breadcrumbs\Domain\BreadcrumbsResolverInterface;
use Tulia\Cms\Platform\Shared\Breadcrumbs\BreadcrumbsInterface;

/**
 * @author Adam Banaszkiewicz
 */
class HomepageBreadcrumbsResolver implements BreadcrumbsResolverInterface
{
    public function __construct(
        private readonly TranslatorInterface $translator,
        private readonly RouterInterface $router,
    ) {
    }

    public function findRootIdentity(Request $request): ?string
    {
        return null;
    }

    public function fillBreadcrumbs(string $identity, string $websiteId, string $locale, BreadcrumbsInterface $breadcrumbs): ?string
    {
        $breadcrumbs->unshift(
            $this->router->generate('frontend.homepage'),
            $this->translator->trans('homepage')
        );

        return null;
    }

    public function supports(string $identity): bool
    {
        return $identity === 'frontend.homepage';
    }
}
