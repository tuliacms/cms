<?php

declare(strict_types=1);

namespace Tulia\Cms\Taxonomy\UserInterface\Web\Backend\Controller;

use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Tulia\Cms\Content\Type\Domain\ReadModel\Service\ContentTypeRegistryInterface;
use Tulia\Cms\Platform\Infrastructure\Framework\Controller\AbstractController;
use Tulia\Cms\Platform\Infrastructure\Framework\Routing\Website\WebsiteInterface;
use Tulia\Cms\Security\Framework\Security\Http\Csrf\Annotation\CsrfToken;
use Tulia\Cms\Taxonomy\Application\UseCase\UpdateTermsHierarchy;
use Tulia\Cms\Taxonomy\Application\UseCase\UpdateTermsHierarchyRequest;
use Tulia\Cms\Taxonomy\Domain\WriteModel\Service\TaxonomyTermsTreeQueryInterface;

/**
 * @author Adam Banaszkiewicz
 */
class Hierarchy extends AbstractController
{
    public function __construct(
        private readonly ContentTypeRegistryInterface $contentTypeRegistry,
        private readonly TaxonomyTermsTreeQueryInterface $termsTreeQuery,
    ) {
    }

    public function index(string $taxonomyType, WebsiteInterface $website): mixed
    {
        $contentType = $this->contentTypeRegistry->get($taxonomyType);

        if (false === $contentType->isHierarchical()) {
            return $this->redirectToRoute('backend.term', ['taxonomyType' => $taxonomyType]);
        }

        return $this->view('@backend/taxonomy/hierarchy/index.tpl', [
            'tree' => $this->termsTreeQuery->getTermsTreeFor($taxonomyType, $website->getId(), $website->getLocale()->getCode()),
            'taxonomyType' => $taxonomyType,
        ]);
    }

    /**
     * @CsrfToken(id="taxonomy_hierarchy")
     */
    public function save(
        Request $request,
        string $taxonomyType,
        UpdateTermsHierarchy $updateTermsHierarchy,
        WebsiteInterface $website,
    ): RedirectResponse {
        $contentType = $this->contentTypeRegistry->get($taxonomyType);

        if (false === $contentType->isHierarchical()) {
            return $this->redirectToRoute('backend.term', ['taxonomyType' => $taxonomyType]);
        }

        $hierarchy = $request->request->all('term');

        if (empty($hierarchy)) {
            return $this->redirectToRoute('backend.term.hierarchy', ['taxonomyType' => $taxonomyType]);
        }

        $updateTermsHierarchy(new UpdateTermsHierarchyRequest($taxonomyType, $website->getId(), $hierarchy));

        $this->addFlash('success', $this->trans('hierarchyUpdated'));
        return $this->redirectToRoute('backend.term.hierarchy', ['taxonomyType' => $taxonomyType]);
    }
}
