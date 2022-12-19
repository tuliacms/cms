<?php

declare(strict_types=1);

namespace Tulia\Cms\Node\UserInterface\Web\Backend\Controller;

use Symfony\Component\Form\FormError;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Tulia\Cms\Content\Type\Domain\ReadModel\Model\ContentType;
use Tulia\Cms\Content\Type\Domain\ReadModel\Service\ContentTypeRegistryInterface;
use Tulia\Cms\Content\Type\Infrastructure\Framework\Form\Service\ContentFormService;
use Tulia\Cms\Node\Application\UseCase\CreateNode;
use Tulia\Cms\Node\Application\UseCase\CreateNodeRequest;
use Tulia\Cms\Node\Application\UseCase\DeleteNode;
use Tulia\Cms\Node\Application\UseCase\UpdateNode;
use Tulia\Cms\Node\Application\UseCase\UpdateNodeRequest;
use Tulia\Cms\Node\Domain\ReadModel\Datatable\NodeDatatableFinderInterface;
use Tulia\Cms\Node\Domain\WriteModel\Exception\CannotDeleteNodeException;
use Tulia\Cms\Node\Domain\WriteModel\Exception\CannotImposePurposeToNodeException;
use Tulia\Cms\Node\Domain\WriteModel\Exception\NodeDoesntExistsException;
use Tulia\Cms\Node\Domain\WriteModel\Service\NodeRepositoryInterface;
use Tulia\Cms\Node\UserInterface\Web\Backend\Form\NodeDetailsForm;
use Tulia\Cms\Platform\Infrastructure\Framework\Controller\AbstractController;
use Tulia\Cms\Security\Framework\Security\Http\Csrf\Annotation\CsrfToken;
use Tulia\Cms\Security\Framework\Security\Http\Csrf\Annotation\IgnoreCsrfToken;
use Tulia\Cms\Security\Framework\Security\Http\Csrf\Exception\RequestCsrfTokenException;
use Tulia\Cms\Shared\Application\UseCase\IdRequest;
use Tulia\Cms\Shared\Domain\WriteModel\Model\ValueObject\ImmutableDateTime;
use Tulia\Cms\User\Application\Service\AuthenticatedUserProviderInterface;
use Tulia\Component\Datatable\DatatableFactory;
use Tulia\Cms\Platform\Infrastructure\Framework\Routing\Website\WebsiteInterface;
use Tulia\Component\Templating\ViewInterface;


/**
 * @author Adam Banaszkiewicz
 */
class Node extends AbstractController
{
    public function __construct(
        private readonly ContentTypeRegistryInterface $typeRegistry,
        private readonly NodeRepositoryInterface $repository,
        private readonly DatatableFactory $factory,
        private readonly NodeDatatableFinderInterface $finder,
        private readonly ContentFormService $contentFormService,
        private readonly AuthenticatedUserProviderInterface $authenticatedUserProvider,
    ) {
    }

    public function index(string $node_type): RedirectResponse
    {
        return $this->redirectToRoute('backend.node.list', ['node_type' => $node_type]);
    }

    public function list(Request $request, string $node_type, WebsiteInterface $website): ViewInterface
    {
        $nodeTypeObject = $this->findNodeType($node_type);

        return $this->view('@backend/node/list.tpl', [
            'nodeType'   => $nodeTypeObject,
            'datatable'  => $this->factory->create($this->finder, $request)->generateFront([
                'website' => $website,
                'node_type' => $this->findNodeType($node_type),
            ]),
            'taxonomies' => $this->collectTaxonomies($nodeTypeObject),
        ]);
    }

    public function datatable(Request $request, string $node_type, WebsiteInterface $website): JsonResponse
    {
        return $this->factory->create($this->finder, $request)->generateResponse([
            'website' => $website,
            'node_type' => $this->findNodeType($node_type),
        ]);
    }

    /**
     * @return RedirectResponse|ViewInterface
     * @IgnoreCsrfToken()
     */
    public function create(
        Request $request,
        CreateNode $createNode,
        string $node_type,
        WebsiteInterface $website,
    ) {
        if (!$website->isDefaultLocale()) {
            $this->addFlash('info', $this->trans('youHaveBeenRedirectedToDefaultLocaleDueToCreationMultilingualElement'));
            return $this->redirectToRoute('backend.node.create', ['node_type' => $node_type, '_locale' => $website->getDefaultLocale()->getCode()]);
        }

        $this->validateCsrfToken($request, $node_type);

        $nodeType = $this->typeRegistry->get($node_type);

        $nodeDetailsForm = $this->createForm(
            NodeDetailsForm::class,
            [
                'author' => $this->authenticatedUserProvider->getUser()->getId(),
                'published_at' => new ImmutableDateTime(),
                'status' => 'published',
            ],
            [
                'content_type' => $nodeType,
                'csrf_protection' => false,
                'website_id' => $website->getId(),
                'locale' => $website->getLocale()->getCode(),
            ]
        );
        $nodeDetailsForm->handleRequest($request);

        $formDescriptor = $this->contentFormService->buildFormDescriptor(
            $website,
            $nodeType->getCode(),
            [],
            [
                'partialView' => '@backend/node/parts/content-type-node-details.tpl',
                'nodeDetailsForm' => $nodeDetailsForm,
            ]
        );
        $formDescriptor->handleRequest($request);
        $nodeType = $formDescriptor->getContentType();

        if ($formDescriptor->isFormValid() && $nodeDetailsForm->isSubmitted() && $nodeDetailsForm->isValid()) {
            try {
                $result = $createNode(new CreateNodeRequest(
                    $node_type,
                    $this->authenticatedUserProvider->getUser()->getId(),
                    $nodeDetailsForm->getData(),
                    $formDescriptor->getData(),
                    $website->getId(),
                    $website->getLocale()->getCode(),
                    $website->getDefaultLocale()->getCode(),
                    $website->getLocaleCodes(),
                ));

                $this->addFlash('success', $this->trans('nodeSaved', [], 'node'));
                return $this->redirectToRoute('backend.node.edit', [ 'id' => $result->id, 'node_type' => $nodeType->getCode() ]);
            }  catch (CannotImposePurposeToNodeException $e) {
                $nodeDetailsForm->get('purposes')->addError(new FormError($this->trans($e->reason)));
            }
        }

        return $this->view('@backend/node/create.tpl', [
            'nodeType' => $nodeType,
            'formDescriptor' => $formDescriptor,
        ]);
    }

    /**
     * @return RedirectResponse|ViewInterface
     * @IgnoreCsrfToken()
     */
    public function edit(
        string $id,
        string $node_type,
        Request $request,
        UpdateNode $updateNode,
        WebsiteInterface $website,
    ) {
        $this->validateCsrfToken($request, $node_type);

        try {
            $node = $this->repository->get($id);
        } catch (NodeDoesntExistsException $e) {
            $this->addFlash('warning', $this->trans('nodeNotFound', [], 'node'));
            return $this->redirectToRoute('backend.node.list');
        }

        $nodeType = $this->typeRegistry->get($node_type);
        $nodeArray = $node->toArray(
            $website->getLocale()->getCode(),
            $website->getDefaultLocale()->getCode(),
        );

        $nodeDetailsForm = $this->createForm(
            NodeDetailsForm::class,
            $nodeArray,
            [
                'content_type' => $nodeType,
                'csrf_protection' => false,
                'website_id' => $website->getId(),
                'locale' => $website->getLocale()->getCode(),
            ]
        );
        $nodeDetailsForm->handleRequest($request);

        $formDescriptor = $this->contentFormService->buildFormDescriptor(
            $website,
            $node_type,
            $nodeArray['attributes'],
            ['nodeDetailsForm' => $nodeDetailsForm]
        );
        $formDescriptor->handleRequest($request);

        if ($formDescriptor->isFormValid() && $nodeDetailsForm->isSubmitted() && $nodeDetailsForm->isValid()) {
            try {
                $updateNode(new UpdateNodeRequest(
                    $node->getId(),
                    $nodeDetailsForm->getData(),
                    $formDescriptor->getData(),
                    $website->getDefaultLocale()->getCode(),
                    $website->getLocale()->getCode(),
                ));
                $this->addFlash('success', $this->trans('nodeSaved', [], 'node'));
                return $this->redirectToRoute('backend.node.edit', [ 'id' => $node->getId(), 'node_type' => $nodeType->getCode() ]);
            } catch (CannotImposePurposeToNodeException $e) {
                $nodeDetailsForm->get('purposes')->addError(new FormError($this->trans($e->reason)));
            }
        }

        return $this->view('@backend/node/edit.tpl', [
            'nodeType' => $nodeType,
            'node'     => $node,
            'formDescriptor' => $formDescriptor,
        ]);
    }

    /**
     * @CsrfToken(id="node.change-status")
     */
    public function changeStatus(Request $request): RedirectResponse
    {
        $nodeType = $this->findNodeType($request->query->get('node_type', 'page'));
        $status = $request->query->get('status');
        $payload = $request->request->all();

        foreach ($payload['ids'] ?? [] as $id) {
            $node = $this->repository->get($id);

            if (!$node) {
                continue;
            }

            switch ($status) {
                case 'trashed'  : $node->setStatus('trashed'); break;
                case 'published': $node->setStatus('published'); break;
                default         : return $this->redirectToRoute('backend.node', [ 'node_type' => $nodeType->getCode() ]);
            }

            $this->repository->save($node);
        }

        switch ($request->query->get('status')) {
            case 'trashed'  : $message = 'selectedNodesWereTrashed'; break;
            case 'published': $message = 'selectedNodesWerePublished'; break;
            default         : $message = 'selectedNodesWereUpdated'; break;
        }

        $this->addFlash('success', $this->trans($message, [], 'node'));
        return $this->redirectToRoute('backend.node', [ 'node_type' => $nodeType->getCode() ]);
    }

    /**
     * @CsrfToken(id="node.delete")
     */
    public function delete(Request $request, DeleteNode $deleteNode): RedirectResponse
    {
        $payload = $request->request->all();
        $pretenders = 0;
        $deleted = 0;

        foreach ($payload['ids'] ?? [] as $id) {
            try {
                $pretenders++;
                ($deleteNode)(new IdRequest($id));
                $deleted++;
            } catch (CannotDeleteNodeException $e) {
                $this->addFlash('danger', $this->trans(
                    'cannotDeleteNodeReason',
                    [
                        'title' => $e->title,
                        'reason' => $this->trans($e->reason, [], 'node')
                    ],
                    'node'
                ));
            }
        }

        if ($pretenders !== 0 && $pretenders === $deleted) {
            $this->addFlash('success', $this->trans('selectedElementsWereDeleted'));
        }

        return $this->redirectToRoute('backend.node', [ 'node_type' => $request->query->get('node_type', 'page') ]);
    }

    protected function findNodeType(string $type): ContentType
    {
        $contentType = $this->typeRegistry->get($type);

        if (! $contentType || $contentType->isType('node') === false) {
            throw $this->createNotFoundException('Node type not found.');
        }

        return $contentType;
    }

    private function collectTaxonomies(ContentType $nodeType): array
    {
        $result = [];

        foreach ($nodeType->getFields() as $field) {
            if ($field->getType() !== 'taxonomy') {
                continue;
            }

            $result[] = $this->typeRegistry->get($field->getConfig('taxonomy'));
        }

        return $result;
    }

    /**
     * @throws RequestCsrfTokenException
     */
    private function validateCsrfToken(Request $request, string $node_type): void
    {
        /**
         * We must detect token validness manually, cause form name changes for every content type.
         */
        if ($request->isMethod('POST')) {
            $tokenId = 'content_builder_form_' . $node_type;
            $csrfToken = $request->request->all()[$tokenId]['_token'] ?? '';

            if ($this->isCsrfTokenValid($tokenId, $csrfToken) === false) {
                throw new RequestCsrfTokenException('CSRF token is invalid. Operation stopped.');
            }
        }
    }
}
