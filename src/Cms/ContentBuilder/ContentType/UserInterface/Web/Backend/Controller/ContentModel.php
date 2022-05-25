<?php

declare(strict_types=1);

namespace Tulia\Cms\ContentBuilder\ContentType\UserInterface\Web\Backend\Controller;

use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Tulia\Cms\ContentBuilder\ContentType\Domain\ReadModel\Service\ContentTypeRegistryInterface;
use Tulia\Cms\ContentBuilder\ContentType\Domain\WriteModel\ContentTypeRepositoryInterface;
use Tulia\Cms\ContentBuilder\ContentType\Domain\WriteModel\Service\ArrayToModelTransformer;
use Tulia\Cms\ContentBuilder\ContentType\Domain\WriteModel\Service\Configuration;
use Tulia\Cms\ContentBuilder\ContentType\Domain\WriteModel\Service\ModelToArrayTransformer;
use Tulia\Cms\ContentBuilder\ContentType\Infrastructure\Framework\Form\ContentType\FormHandler;
use Tulia\Cms\ContentBuilder\Layout\Service\LayoutTypeBuilderRegistry;
use Tulia\Cms\Platform\Infrastructure\Framework\Controller\AbstractController;
use Tulia\Cms\Security\Framework\Security\Http\Csrf\Annotation\CsrfToken;
use Tulia\Component\Templating\ViewInterface;

/**
 * @author Adam Banaszkiewicz
 */
class ContentModel extends AbstractController
{
    private ArrayToModelTransformer $arrayToModelTransformer;
    private ContentTypeRepositoryInterface $contentTypeRepository;
    private ContentTypeRegistryInterface $contentTypeRegistry;
    private Configuration $configuration;
    private LayoutTypeBuilderRegistry $layoutTypeBuilderRegistry;

    public function __construct(
        ArrayToModelTransformer $arrayToModelTransformer,
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
            $layoutType = $this->arrayToModelTransformer->produceLayoutType($data);
            $nodeType = $this->arrayToModelTransformer->produceContentType($data, $contentType, $layoutType);

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

        $contentType = $this->contentTypeRepository->find($id);

        if ($contentType === null) {
            $this->setFlash('danger', $this->trans('contentTypeNotExists', [], 'content_builder'));
            return $this->redirectToRoute('backend.content.type.homepage');
        }

        $layout = $contentType->getLayout();

        $data = (new ModelToArrayTransformer())->transform($contentType, $layout);
        $data = $nodeTypeFormHandler->handle($request, $data, true);

        if ($nodeTypeFormHandler->isRequestValid()) {
            $layoutType = $this->arrayToModelTransformer->produceLayoutType($data);
            $nodeType = $this->arrayToModelTransformer->produceContentType($data, $contentType->getType(), $layoutType);

            try {
                $this->contentTypeRepository->update($nodeType);
            } catch (\Exception $e) {
                dump($e);exit;
            }

            $this->setFlash('success', $this->trans('contentTypeUpdatedSuccessfully', [], 'content_builder'));
            return $this->redirectToRoute('backend.content.type.homepage');
        }

        $layoutBuilder = $this->layoutTypeBuilderRegistry->get($this->configuration->getLayoutBuilder($contentType->getType()));

        return $this->view('@backend/content_builder/content_type/edit.tpl', [
            'type' => $contentType->getType(),
            'builderView' => $layoutBuilder->builderView($contentType->getType(), $data, $nodeTypeFormHandler->getErrors(), false),
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
