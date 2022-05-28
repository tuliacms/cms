<?php

declare(strict_types=1);

namespace Tulia\Cms\Content\Type\UserInterface\Web\Backend\Controller;

use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Tulia\Cms\Content\Type\Domain\ReadModel\Service\ContentTypeRegistryInterface;
use Tulia\Cms\Content\Type\Domain\WriteModel\ContentTypeRepositoryInterface;
use Tulia\Cms\Content\Type\Domain\WriteModel\Service\ArrayToWriteModelTransformer;
use Tulia\Cms\Content\Type\Domain\WriteModel\Service\Configuration;
use Tulia\Cms\Content\Type\Domain\WriteModel\Service\ModelToArrayTransformer;
use Tulia\Cms\Content\Type\Infrastructure\Framework\Form\ContentType\FormHandler;
use Tulia\Cms\Content\Type\Infrastructure\Framework\Form\Service\LayoutTypeBuilderRegistry;
use Tulia\Cms\Platform\Infrastructure\Framework\Controller\AbstractController;
use Tulia\Cms\Security\Framework\Security\Http\Csrf\Annotation\CsrfToken;
use Tulia\Component\Templating\ViewInterface;

/**
 * @author Adam Banaszkiewicz
 */
class ContentModel extends AbstractController
{
    private ArrayToWriteModelTransformer $arrayToModelTransformer;
    private ContentTypeRepositoryInterface $contentTypeRepository;
    private ContentTypeRegistryInterface $contentTypeRegistry;
    private Configuration $configuration;
    private LayoutTypeBuilderRegistry $layoutTypeBuilderRegistry;

    public function __construct(
        ArrayToWriteModelTransformer $arrayToModelTransformer,
        ContentTypeRepositoryInterface $contentTypeRepository,
        ContentTypeRegistryInterface $contentTypeRegistry,
        Configuration $configuration,
        LayoutTypeBuilderRegistry $layoutTypeBuilderRegistry
    ) {
        $this->arrayToModelTransformer = $arrayToModelTransformer;
        $this->contentTypeRepository = $contentTypeRepository;
        $this->contentTypeRegistry = $contentTypeRegistry;
        $this->configuration = $configuration;
        $this->layoutTypeBuilderRegistry = $layoutTypeBuilderRegistry;
    }

    public function index(): ViewInterface
    {
        return $this->view('@backend/content_builder/index.tpl', [
            'contentTypeList' => iterator_to_array($this->contentTypeRegistry->all()),
            'contentTypeCodes' => $this->configuration->getConfigurableTypes(),
        ]);
    }

    /**
     * @CsrfToken(id="create-content-type")
     * @return ViewInterface|RedirectResponse
     */
    public function create(string $contentType, Request $request, FormHandler $nodeTypeFormHandler)
    {
        if ($this->configuration->typeExists($contentType) === false) {
            $this->addFlash('danger', $this->trans('contentTypeOfNotExists', ['name' => $contentType], 'content_builder'));
            return $this->redirectToRoute('backend.content.type.homepage');
        }

        if ($request->isMethod('POST')) {
            $data = json_decode($request->request->get('node_type'), true);
        } else {
            $data = [];
        }

        $data = $nodeTypeFormHandler->handle($request, $data);

        if ($nodeTypeFormHandler->isRequestValid()) {
            $nodeType = $this->arrayToModelTransformer->produceContentType($contentType, $data);

            try {
                $this->contentTypeRepository->insert($nodeType);
            } catch (\Exception $e) {
                dump($e);exit;
            }

            $this->setFlash('success', $this->trans('contentTypeCreatedSuccessfully', [], 'content_builder'));
            return $this->redirectToRoute('backend.content.type.homepage');
        }

        $layoutBuilder = $this->layoutTypeBuilderRegistry->get($this->configuration->getLayoutBuilder($contentType));

        return $this->view('@backend/content_builder/content_type/create.tpl', [
            'type' => $contentType,
            'builderView' => $layoutBuilder->builderView($contentType, $data, $nodeTypeFormHandler->getErrors(), true),
            'cleaningResult' => $nodeTypeFormHandler->getCleaningResult(),
        ]);
    }

    /**
     * @CsrfToken(id="create-content-type")
     * @return ViewInterface|RedirectResponse
     */
    public function edit(
        string $id,
        string $contentType,
        Request $request,
        FormHandler $nodeTypeFormHandler
    ) {
        if ($this->configuration->typeExists($contentType) === false) {
            $this->addFlash('danger', $this->trans('contentTypeOfNotExists', ['name' => $contentType], 'content_builder'));
            return $this->redirectToRoute('backend.content.type.homepage');
        }

        $contentTypeAggregate = $this->contentTypeRepository->find($id);

        if ($contentTypeAggregate === null) {
            $this->setFlash('danger', $this->trans('contentTypeNotExists', [], 'content_builder'));
            return $this->redirectToRoute('backend.content.type.homepage');
        }

        $data = (new ModelToArrayTransformer())->transform($contentTypeAggregate);
        $data = $nodeTypeFormHandler->handle($request, $data, true);

        if ($nodeTypeFormHandler->isRequestValid()) {
            $contentTypeAggregate = $this->arrayToModelTransformer->fillContentType($contentTypeAggregate, $data);

            try {
                $this->contentTypeRepository->update($contentTypeAggregate);
            } catch (\Exception $e) {
                dump($e);exit;
            }

            $this->setFlash('success', $this->trans('contentTypeUpdatedSuccessfully', [], 'content_builder'));
            return $this->redirectToRoute('backend.content.type.content_type.edit', ['contentType' => $contentType, 'id' => $contentTypeAggregate->getId()]);
        }

        $layoutBuilder = $this->layoutTypeBuilderRegistry->get($this->configuration->getLayoutBuilder($contentType));

        return $this->view('@backend/content_builder/content_type/edit.tpl', [
            'type' => $contentType,
            'builderView' => $layoutBuilder->builderView($contentType, $data, $nodeTypeFormHandler->getErrors(), false),
            'cleaningResult' => $nodeTypeFormHandler->getCleaningResult(),
        ]);
    }

    /**
     * @CsrfToken(id="delete-content-type")
     */
    public function delete(string $id): RedirectResponse
    {
        $contentType = $this->contentTypeRepository->find($id);

        if ($contentType === null) {
            $this->setFlash('danger', $this->trans('contentTypeNotExists', [], 'content_builder'));
            return $this->redirectToRoute('backend.content.type.homepage');
        }

        $this->contentTypeRepository->delete($contentType);

        $this->setFlash('success', $this->trans('contentTypeWasRemoved', [], 'content_builder'));
        return $this->redirectToRoute('backend.content.type.homepage');
    }
}
