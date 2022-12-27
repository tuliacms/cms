<?php

declare(strict_types=1);

namespace Tulia\Cms\Breadcrumbs\Domain;

use Symfony\Component\HttpFoundation\Request;
use Tulia\Cms\Platform\Shared\Breadcrumbs\BreadcrumbsInterface;

/**
 * @author Adam Banaszkiewicz
 */
interface BreadcrumbsResolverInterface
{
    public function findRootIdentity(Request $request): ?string;
    public function fillBreadcrumbs(string $identity, string $websiteId, string $locale, BreadcrumbsInterface $breadcrumbs): ?string;
    public function supports(string $identity): bool;
}
