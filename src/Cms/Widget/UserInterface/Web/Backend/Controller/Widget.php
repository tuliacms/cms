<?php

declare(strict_types=1);

namespace Tulia\Cms\Widget\UserInterface\Web\Backend\Controller;

use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Tulia\Cms\Content\Type\UserInterface\Web\Backend\Form\FormAttributesExtractor;
use Tulia\Cms\Platform\Infrastructure\Framework\Controller\AbstractController;
use Tulia\Cms\Platform\Infrastructure\Framework\Routing\Website\WebsiteInterface;
use Tulia\Cms\Security\Framework\Security\Http\Csrf\Annotation\CsrfToken;
use Tulia\Cms\Security\Framework\Security\Http\Csrf\Exception\RequestCsrfTokenException;
use Tulia\Cms\Shared\Application\UseCase\IdResult;
use Tulia\Cms\Widget\Application\UseCase\CreateWidget;
use Tulia\Cms\Widget\Application\UseCase\CreateWidgetRequest;
use Tulia\Cms\Widget\Application\UseCase\DeleteWidget;
use Tulia\Cms\Widget\Application\UseCase\DeleteWidgetRequest;
use Tulia\Cms\Widget\Application\UseCase\UpdateWidget;
use Tulia\Cms\Widget\Application\UseCase\UpdateWidgetRequest;
use Tulia\Cms\Widget\Domain\Catalog\Registry\WidgetRegistryInterface;
use Tulia\Cms\Widget\Domain\WriteModel\Exception\WidgetNotFoundException;
use Tulia\Cms\Widget\Domain\WriteModel\WidgetRepositoryInterface;
use Tulia\Cms\Widget\Infrastructure\Persistence\Doctrine\Dbal\DbalDatatableFinder;
use Tulia\Cms\Widget\UserInterface\Web\Backend\Form\WidgetDetailsForm;
use Tulia\Component\Datatable\DatatableFactory;
use Tulia\Component\Templating\ViewInterface;

/**
 * @author Adam Banaszkiewicz
 */
class Widget extends AbstractController
{
    public function __construct(
        private readonly WidgetRegistryInterface $widgetRegistry,
        private readonly WidgetRepositoryInterface $repository,
    ) {
    }

    /**
     * @return RedirectResponse
     */
    public function index(): RedirectResponse
    {
        return $this->redirectToRoute('backend.widget.list');
    }

    public function list(
        Request $request,
        DatatableFactory $factory,
        DbalDatatableFinder $finder,
        WebsiteInterface $website
    ): ViewInterface {
        return $this->view('@backend/widget/list.tpl', [
            'space' => $request->query->get('space', ''),
            'availableWidgets' => $this->widgetRegistry->all(),
            'datatable' => $factory->create($finder, $request)->generateFront(['website' => $website]),
        ]);
    }

    public function datatable(
        Request $request,
        DatatableFactory $factory,
        DbalDatatableFinder $finder,
        WebsiteInterface $website
    ): JsonResponse {
        return $factory->create($finder, $request)->generateResponse(['website' => $website]);
    }

    /**
     * @return RedirectResponse|ViewInterface
     * @throws RequestCsrfTokenException
     * @CsrfToken(id="widget_details_form")
     */
    public function create(
        Request $request,
        string $type,
        CreateWidget $createWidget,
        WebsiteInterface $website,
        FormAttributesExtractor $extractor,
    ) {
        if (!$website->isDefaultLocale()) {
            $this->addFlash('info', $this->trans('youHaveBeenRedirectedToDefaultLocaleDueToCreationMultilingualElement'));
            return $this->redirectToRoute('backend.widget.create', ['type' => $type, '_locale' => $website->getDefaultLocale()->getCode()]);
        }

        $contentType = $this->produceContentType($type);
        $form = $this->createForm(
            WidgetDetailsForm::class,
            [],
            [
                'partial_view' => '@backend/widget/parts/content-type-widget-details.tpl',
                'website' => $website,
                'content_type' => $contentType,
            ]
        );
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            /** @var IdResult $result */
            $result = ($createWidget)(new CreateWidgetRequest(
                $type,
                $extractor->extractData($form, $contentType),
                $website->getId(),
                $website->getLocale()->getCode(),
                $website->getDefaultLocale()->getCode(),
                $website->getLocaleCodes(),
            ));

            $this->addFlash('success', $this->trans('widgetSaved', [], 'widgets'));
            return $this->redirectToRoute('backend.widget.edit', ['id' => $result->id]);
        }

        return $this->view('@backend/widget/create.tpl', [
            'widgetInfo' => $this->widgetRegistry->get($type),
            'form' => $form->createView(),
        ]);
    }

    /**
     * @return RedirectResponse|ViewInterface
     * @throws RequestCsrfTokenException
     * @CsrfToken(id="widget_details_form")
     */
    public function edit(
        Request $request,
        string $id,
        UpdateWidget $updateWidget,
        WebsiteInterface $website,
        FormAttributesExtractor $extractor,
    ) {
        try {
            $widget = $this->repository->get($id);
        } catch (WidgetNotFoundException $e) {
            $this->addFlash('danger', $this->trans('widgetNotFound', [], 'widgets'));
            return $this->redirectToRoute('backend.widget');
        }

        $contentType = $this->produceContentType($widget->getType());
        $widgetInfo = $this->widgetRegistry->get($widget->getType());
        $widgetData = $widget->toArray($website->getLocale()->getCode(), $website->getDefaultLocale()->getCode());

        $form = $this->createForm(
            WidgetDetailsForm::class,
            $widgetData,
            [
                'partial_view' => '@backend/widget/parts/content-type-widget-details.tpl',
                'website' => $website,
                'content_type' => $contentType,
            ]
        );
        $form->handleRequest($request);


        if ($form->isSubmitted() && $form->isValid()) {
            ($updateWidget)(new UpdateWidgetRequest(
                $id,
                $extractor->extractData($form, $contentType),
                $website->getLocale()->getCode(),
                $website->getDefaultLocale()->getCode(),
                $website->getLocaleCodes(),
            ));

            $this->addFlash('success', $this->trans('widgetSaved', [], 'widgets'));
            return $this->redirectToRoute('backend.widget.edit', ['id' => $id]);
        }

        return $this->view('@backend/widget/edit.tpl', [
            'widgetTranslated' => $widget->isTranslatedTo($website->getLocale()->getCode()),
            'widgetInfo' => $widgetInfo,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @param Request $request
     * @return RedirectResponse
     * @CsrfToken(id="widget.delete")
     */
    public function delete(Request $request, DeleteWidget $deleteWidget): RedirectResponse
    {
        $removedWidgets = 0;

        foreach ($request->request->all('ids') as $id) {
            try {
                ($deleteWidget)(new DeleteWidgetRequest($id));
                $removedWidgets++;
            } catch (WidgetNotFoundException $e) {
                continue;
            }
        }

        if ($removedWidgets) {
            $this->addFlash('success', $this->trans('selectedWidgetsWereDeleted', [], 'widgets'));
        }

        return $this->redirectToRoute('backend.widget');
    }

    private function produceContentType(string $type): string
    {
        return str_replace('.', '_', 'widget_'.$type);
    }
}
