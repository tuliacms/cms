<?php

declare(strict_types=1);

namespace Tulia\Cms\Taxonomy\UserInterface\Web\Backend\Controller;

use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Tulia\Cms\Content\Type\Domain\ReadModel\Service\ContentTypeRegistryInterface;
use Tulia\Cms\Content\Type\UserInterface\Web\Backend\Form\FormAttributesExtractor;
use Tulia\Cms\Platform\Infrastructure\Framework\Controller\AbstractController;
use Tulia\Cms\Platform\Infrastructure\Framework\Routing\Website\WebsiteInterface;
use Tulia\Cms\Security\Framework\Security\Http\Csrf\Annotation\CsrfToken;
use Tulia\Cms\Shared\Application\UseCase\IdResult;
use Tulia\Cms\Taxonomy\Application\UseCase\CreateTerm;
use Tulia\Cms\Taxonomy\Application\UseCase\CreateTermRequest;
use Tulia\Cms\Taxonomy\Application\UseCase\UpdateTerm;
use Tulia\Cms\Taxonomy\Application\UseCase\UpdateTermRequest;
use Tulia\Cms\Taxonomy\Domain\ReadModel\Finder\TermFinderInterface;
use Tulia\Cms\Taxonomy\Domain\ReadModel\Service\Datatable\TermDatatableFinderInterface;
use Tulia\Cms\Taxonomy\Domain\ReadModel\Service\TermsOfTaxonomiesQueryInterface;
use Tulia\Cms\Taxonomy\Domain\WriteModel\Exception\TermNotFoundException;
use Tulia\Cms\Taxonomy\Domain\WriteModel\Service\TaxonomyRepositoryInterface;
use Tulia\Cms\Taxonomy\UserInterface\Web\Backend\Form\TaxonomyTermDetailsForm;
use Tulia\Component\Datatable\DatatableFactory;
use Tulia\Component\Templating\ViewInterface;

/**
 * @author Adam Banaszkiewicz
 */
class Term extends AbstractController
{
    public function __construct(
        private readonly TermFinderInterface $termFinder,
        private readonly TaxonomyRepositoryInterface $repository,
        private readonly DatatableFactory $factory,
        private readonly TermDatatableFinderInterface $finder,
        private readonly ContentTypeRegistryInterface $typeRegistry,
        private readonly TermsOfTaxonomiesQueryInterface $termsOfTaxonomiesQuery,
    ) {
    }

    public function listTermsOfTaxonomies(WebsiteInterface $website): JsonResponse
    {
        return new JsonResponse($this->termsOfTaxonomiesQuery->allTermsGrouppedByTaxonomy($website->getId(), $website->getLocale()->getCode()));
    }

    public function index(string $taxonomyType): RedirectResponse
    {
        return $this->redirectToRoute('backend.term.list', ['taxonomyType' => $taxonomyType]);
    }

    /**
     * @return RedirectResponse|ViewInterface
     * @throws NotFoundHttpException
     */
    public function list(Request $request, string $taxonomyType, WebsiteInterface $website)
    {
        return $this->view('@backend/taxonomy/term/list.tpl', [
            'taxonomyType' => $this->typeRegistry->get($taxonomyType),
            'datatable' => $this->factory->create($this->finder, $request)->generateFront([
                'taxonomyType' => $taxonomyType,
                'website' => $website
            ]),
        ]);
    }

    public function datatable(Request $request, string $taxonomyType, WebsiteInterface $website): JsonResponse
    {
        return $this->factory->create($this->finder, $request)->generateResponse([
            'taxonomyType' => $taxonomyType,
            'website' => $website,
        ]);
    }

    /**
     * @return RedirectResponse|ViewInterface
     * @CsrfToken(id="taxonomy_term_details_form")
     */
    public function create(
        Request $request,
        string $taxonomyType,
        CreateTerm $createTerm,
        WebsiteInterface $website,
        FormAttributesExtractor $extractor,
    ) {
        if (!$website->isDefaultLocale()) {
            $this->addFlash('info', $this->trans('youHaveBeenRedirectedToDefaultLocaleDueToCreationMultilingualElement'));
            return $this->redirectToRoute('backend.term.create', ['taxonomyType' => $taxonomyType, '_locale' => $website->getDefaultLocale()->getCode()]);
        }

        $form = $this->createForm(
            TaxonomyTermDetailsForm::class,
            [],
            [
                'partial_view' => '@backend/taxonomy/term/parts/content-type-term-details.tpl',
                'website' => $website,
                'content_type' => $taxonomyType,
                'form_mode' => 'create',
            ]
        );
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            ($createTerm)(new CreateTermRequest(
                $taxonomyType,
                $extractor->extractData($form, $taxonomyType),
                $website->getId(),
                $website->getLocale()->getCode(),
                $website->getDefaultLocale()->getCode(),
                $website->getLocaleCodes(),
            ));

            $this->addFlash('success', $this->trans('termSaved', [], 'taxonomy'));
            return $this->redirectToRoute('backend.term', [ 'taxonomyType' => $taxonomyType ]);
        }

        return $this->view('@backend/taxonomy/term/create.tpl', [
            'form' => $form->createView(),
            'taxonomyType' => $this->typeRegistry->get($taxonomyType),
        ]);
    }

    /**
     * @return RedirectResponse|ViewInterface
     * @throws NotFoundHttpException
     * @CsrfToken(id="taxonomy_term_details_form")
     */
    public function edit(
        Request $request,
        string $taxonomyType,
        string $id,
        UpdateTerm $updateTerm,
        WebsiteInterface $website,
        FormAttributesExtractor $extractor,
    ) {
        $taxonomy = $this->repository->get(
            $taxonomyType,
            $website->getId(),
        );

        try {
            $term = $taxonomy->termToArray($id, $website->getLocale()->getCode());
        } catch (TermNotFoundException $e) {
            $this->addFlash('warning', $this->trans('termNotFound', [], 'categories'));
            return $this->redirectToRoute('backend.term.list', ['taxonomyType' => $taxonomyType]);
        }

        $form = $this->createForm(
            TaxonomyTermDetailsForm::class,
            $term,
            [
                'partial_view' => '@backend/taxonomy/term/parts/content-type-term-details.tpl',
                'website' => $website,
                'content_type' => $taxonomy->getType(),
                'form_mode' => 'edit',
            ]
        );
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            ($updateTerm)(new UpdateTermRequest(
                $taxonomy->getType(),
                $id,
                $extractor->extractData($form, $taxonomyType),
                $website->getId(),
                $website->getLocale()->getCode(),
                $website->getDefaultLocale()->getCode(),
            ));

            $this->addFlash('success', $this->trans('termSaved', [], 'taxonomy'));
            return $this->redirectToRoute('backend.term.edit', [ 'id' => $id, 'taxonomyType' => $taxonomy->getType() ]);
        }

        return $this->view('@backend/taxonomy/term/edit.tpl', [
            'term' => $term,
            'form' => $form->createView(),
            'taxonomyType' => $this->typeRegistry->get($taxonomy->getType()),
        ]);
    }

    /**
     * @return RedirectResponse
     * @CsrfToken(id="term.delete")
     */
    public function delete(Request $request, string $taxonomyType, WebsiteInterface $website): RedirectResponse
    {
        $taxonomy = $this->repository->get(
            $taxonomyType,
            $website->getId(),
        );

        $removedTerms = 0;

        foreach ($request->request->all('ids') as $id) {
            try {
                $taxonomy->deleteTerm($id);
                $removedTerms++;
            } catch (TermNotFoundException $e) {
                continue;
            }
        }

        if ($removedTerms) {
            $this->repository->save($taxonomy);
            $this->addFlash('success', $this->trans('selectedTermsWereDeleted', [], 'taxonomy'));
        }

        return $this->redirectToRoute('backend.term', [ 'taxonomyType' => $taxonomy->getType() ]);
    }
}
