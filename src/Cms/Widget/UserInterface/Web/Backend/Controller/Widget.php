<?php

declare(strict_types=1);

namespace Tulia\Cms\Widget\UserInterface\Web\Backend\Controller;

use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Tulia\Cms\Content\Type\Infrastructure\Framework\Form\ContentTypeFormDescriptor;
use Tulia\Cms\Content\Type\Infrastructure\Framework\Form\Service\ContentFormService;
use Tulia\Cms\Platform\Infrastructure\Framework\Controller\AbstractController;
use Tulia\Cms\Security\Framework\Security\Http\Csrf\Annotation\CsrfToken;
use Tulia\Cms\Security\Framework\Security\Http\Csrf\Annotation\IgnoreCsrfToken;
use Tulia\Cms\Security\Framework\Security\Http\Csrf\Exception\RequestCsrfTokenException;
use Tulia\Cms\Widget\Application\UseCase\CreateWidget;
use Tulia\Cms\Widget\Application\UseCase\CreateWidgetRequest;
use Tulia\Cms\Widget\Application\UseCase\DeleteWidget;
use Tulia\Cms\Widget\Application\UseCase\DeleteWidgetRequest;
use Tulia\Cms\Widget\Application\UseCase\UpdateWidget;
use Tulia\Cms\Widget\Application\UseCase\UpdateWidgetRequest;
use Tulia\Cms\Widget\Domain\Catalog\Registry\WidgetRegistryInterface;
use Tulia\Cms\Widget\Domain\WriteModel\Exception\WidgetNotFoundException;
use Tulia\Cms\Widget\Domain\WriteModel\WidgetRepositoryInterface;
use Tulia\Cms\Widget\Infrastructure\Persistence\Domain\ReadModel\Datatable\DatatableFinder;
use Tulia\Cms\Widget\UserInterface\Web\Backend\Form\WidgetDetailsForm;
use Tulia\Component\Datatable\DatatableFactory;
use Tulia\Component\Routing\Website\WebsiteInterface;
use Tulia\Component\Templating\ViewInterface;

/**
 * @author Adam Banaszkiewicz
 */
class Widget extends AbstractController
{
    public function __construct(
        private WidgetRegistryInterface $widgetRegistry,
        private WidgetRepositoryInterface $repository,
        private ContentFormService $contentFormService
    ) {
    }

    /**
     * @return RedirectResponse
     */
    public function index(): RedirectResponse
    {
        return $this->redirectToRoute('backend.widget.list');
    }

    public function list(Request $request, DatatableFactory $factory, DatatableFinder $finder): ViewInterface
    {
        return $this->view('@backend/widget/list.tpl', [
            'space' => $request->query->get('space', ''),
            'availableWidgets' => $this->widgetRegistry->all(),
            'datatable' => $factory->create($finder, $request),
        ]);
    }

    public function datatable(Request $request, DatatableFactory $factory, DatatableFinder $finder): JsonResponse
    {
        return $factory->create($finder, $request)->generateResponse();
    }

    /**
     * @return RedirectResponse|ViewInterface
     * @IgnoreCsrfToken()
     * @throws RequestCsrfTokenException
     */
    public function create(Request $request, string $type, CreateWidget $createWidget, WebsiteInterface $website)
    {
        $this->validateCsrfToken($request, $type);

        $widgetInfo = $this->widgetRegistry->get($type);

        $widgetDetailsForm = $this->createForm(WidgetDetailsForm::class, [], ['csrf_protection' => false]);
        $widgetDetailsForm->handleRequest($request);

        $formDescriptor = $this->produceFormDescriptor($type, [], $widgetDetailsForm);
        $formDescriptor->handleRequest($request);

        if ($formDescriptor->isFormValid()) {
            ($createWidget)(new CreateWidgetRequest(
                $type,
                $widgetDetailsForm->getData(),
                $formDescriptor->getData(),
                $website->getLocale()->getCode(),
                $website->getDefaultLocale()->getCode(),
                $website->getLocaleCodes(),
            ));

            $this->setFlash('success', $this->trans('widgetSaved', [], 'widgets'));
            return $this->redirectToRoute('backend.widget');
        }

        return $this->view('@backend/widget/create.tpl', [
            'widgetInfo' => $widgetInfo,
            'formDescriptor' => $formDescriptor,
        ]);
    }

    /**
     * @return RedirectResponse|ViewInterface
     * @IgnoreCsrfToken()
     * @throws RequestCsrfTokenException
     */
    public function edit(Request $request, string $id, UpdateWidget $updateWidget, WebsiteInterface $website)
    {
        try {
            $widget = $this->repository->get($id);
        } catch (WidgetNotFoundException $e) {
            $this->setFlash('danger', $this->trans('widgetNotFound', [], 'widgets'));
            return $this->redirectToRoute('backend.widget');
        }

        $this->validateCsrfToken($request, $widget->getType());

        $widgetInfo = $this->widgetRegistry->get($widget->getType());
        $widgetData = $widget->toArray($website->getLocale()->getCode(), $website->getDefaultLocale()->getCode());

        $widgetDetailsForm = $this->createForm(
            WidgetDetailsForm::class,
            $widgetData,
            ['csrf_protection' => false]
        );
        $widgetDetailsForm->handleRequest($request);

        $formDescriptor = $this->produceFormDescriptor($widget->getType(), $widgetData['attributes'], $widgetDetailsForm);
        $formDescriptor->handleRequest($request);

        if ($formDescriptor->isFormValid()) {
            ($updateWidget)(new UpdateWidgetRequest(
                $id,
                $widgetDetailsForm->getData(),
                $formDescriptor->getData(),
                $website->getLocale()->getCode(),
                $website->getDefaultLocale()->getCode(),
                $website->getLocaleCodes(),
            ));

            $this->setFlash('success', $this->trans('widgetSaved', [], 'widgets'));
            return $this->redirectToRoute('backend.widget');
        }

        return $this->view('@backend/widget/edit.tpl', [
            'widgetTranslated' => $widget->isTranslatedTo($website->getLocale()->getCode()),
            'widgetInfo' => $widgetInfo,
            'formDescriptor' => $formDescriptor,
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
            $this->setFlash('success', $this->trans('selectedWidgetsWereDeleted', [], 'widgets'));
        }

        return $this->redirectToRoute('backend.widget');
    }

    private function produceFormDescriptor(string $type, array $attributes, FormInterface $widgetDetailsForm): ContentTypeFormDescriptor
    {
        return $this->contentFormService->buildFormDescriptor(
            str_replace('.', '_', 'widget_'.$type),
            $attributes,
            ['widgetDetailsForm' => $widgetDetailsForm]
        );
    }

    /**
     * @throws RequestCsrfTokenException
     */
    private function validateCsrfToken(Request $request, string $type): void
    {
        /**
         * We must detect token validness manually, cause form name changes for every content type.
         */
        if ($request->isMethod('POST')) {
            $tokenId = 'content_builder_form_widget_' . str_replace('.', '_', $type);
            $csrfToken = $request->request->all()[$tokenId]['_token'] ?? '';

            if ($this->isCsrfTokenValid($tokenId, $csrfToken) === false) {
                throw new RequestCsrfTokenException('CSRF token is invalid. Operation stopped.');
            }
        }
    }
}
