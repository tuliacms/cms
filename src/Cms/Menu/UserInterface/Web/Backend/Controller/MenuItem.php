<?php

declare(strict_types=1);

namespace Tulia\Cms\Menu\UserInterface\Web\Backend\Controller;

use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Tulia\Cms\Content\Type\Infrastructure\Framework\Form\ContentTypeFormDescriptor;
use Tulia\Cms\Content\Type\Infrastructure\Framework\Form\Service\ContentFormService;
use Tulia\Cms\Menu\Application\UseCase\CreateMenuItem;
use Tulia\Cms\Menu\Application\UseCase\CreateMenuItemRequest;
use Tulia\Cms\Menu\Application\UseCase\DeleteMenuItem;
use Tulia\Cms\Menu\Application\UseCase\DeleteMenuItemRequest;
use Tulia\Cms\Menu\Application\UseCase\UpdateMenu;
use Tulia\Cms\Menu\Application\UseCase\UpdateMenuItem;
use Tulia\Cms\Menu\Application\UseCase\UpdateMenuItemRequest;
use Tulia\Cms\Menu\Domain\Builder\Type\RegistryInterface;
use Tulia\Cms\Menu\Domain\ReadModel\Datatable\ItemDatatableFinderInterface;
use Tulia\Cms\Menu\Domain\WriteModel\Exception\MenuItemDoesntExistsException;
use Tulia\Cms\Menu\Domain\WriteModel\Exception\MenuNotExistsException;
use Tulia\Cms\Menu\Domain\WriteModel\MenuRepositoryInterface;
use Tulia\Cms\Menu\UserInterface\Web\Backend\Form\MenuItemDetailsForm;
use Tulia\Cms\Platform\Infrastructure\Framework\Controller\AbstractController;
use Tulia\Cms\Security\Framework\Security\Http\Csrf\Annotation\CsrfToken;
use Tulia\Component\Datatable\DatatableFactory;
use Tulia\Component\Routing\Website\WebsiteInterface;
use Tulia\Component\Templating\ViewInterface;

/**
 * @author Adam Banaszkiewicz
 */
class MenuItem extends AbstractController
{
    public function __construct(
        private MenuRepositoryInterface $repository,
        private RegistryInterface $menuTypeRegistry,
        private DatatableFactory $factory,
        private ItemDatatableFinderInterface $finder,
        private ContentFormService $contentFormService,
    ) {
    }

    public function index(string $menuId): RedirectResponse
    {
        return $this->redirectToRoute('backend.menu.item.list', [
            'menuId' => $menuId,
        ]);
    }

    public function list(Request $request, string $menuId, WebsiteInterface $website)
    {
        $menu = $this->repository->find($menuId);

        if (!$menu) {
            $this->setFlash('danger', $this->trans('menuNotFound', [], 'menu'));
            return $this->redirectToRoute('backend.menu');
        }

        return $this->view('@backend/menu/item/list.tpl', [
            'menu' => $menu,
            'datatable' => $this->factory->create($this->finder, $request)->generateFront([
                'website' => $website,
                'menu_id' => $menuId,
            ]),
        ]);
    }

    public function datatable(Request $request, string $menuId, WebsiteInterface $website): JsonResponse
    {
        return $this->factory->create($this->finder, $request)->generateResponse([
            'website' => $website,
            'menu_id' => $menuId,
        ]);
    }

    /**
     * @return RedirectResponse|ViewInterface
     * @CsrfToken(id="content_builder_form_menu_item")
     */
    public function create(
        Request $request,
        CreateMenuItem $createMenuItem,
        WebsiteInterface $website,
        string $menuId,
    ) {
        try {
            $menu = $this->repository->get($menuId);
        } catch (MenuNotExistsException $e) {
            $this->setFlash('danger', $this->trans('menuNotFound', [], 'menu'));
            return $this->redirectToRoute('backend.menu');
        }

        $itemDetailsForm = $this->createForm(
            MenuItemDetailsForm::class,
            [],
            [
                'csrf_protection' => false,
                'menu_id' => $menuId,
                'locale' => $website->getLocale()->getCode(),
                'website_id' => $website->getId(),
            ]
        );
        $itemDetailsForm->handleRequest($request);

        $formDescriptor = $this->contentFormService->buildFormDescriptor(
            $website,
            'menu_item',
            [],
            [
                'itemDetailsForm' => $itemDetailsForm->createView(),
                'item' => $this->getEmptyItem(),
                'types' => $this->collectMenuTypes(),
                'website_id' => $website->getId(),
                'locale' => $website->getLocale()->getCode(),
            ]
        );
        $formDescriptor->handleRequest($request);

        if ($formDescriptor->isFormValid()) {
            ($createMenuItem)(new CreateMenuItemRequest(
                $menuId,
                $itemDetailsForm->getData(),
                $formDescriptor->getData(),
                $website->getLocale()->getCode(),
                $website->getLocaleCodes(),
            ));

            $this->setFlash('success', $this->trans('itemSaved', [], 'menu'));
            return $this->redirectToRoute('backend.menu.item', [ 'menuId' => $menu->getId() ]);
        }

        return $this->view('@backend/menu/item/create.tpl', [
            'menu'  => $menu,
            'formDescriptor' => $formDescriptor,
        ]);
    }

    /**
     * @return RedirectResponse|ViewInterface
     * @CsrfToken(id="content_builder_form_menu_item")
     */
    public function edit(
        Request $request,
        UpdateMenuItem $updateMenuItem,
        WebsiteInterface $website,
        string $menuId,
        string $id
    ) {
        try {
            $menu = $this->repository->get($menuId);
            $item = $menu->getItem($id);
        } catch (MenuNotExistsException $e) {
            $this->setFlash('danger', $this->trans('menuNotFound', [], 'menu'));
            return $this->redirectToRoute('backend.menu');
        } catch (MenuItemDoesntExistsException $e) {
            $this->setFlash('danger', $this->trans('menuItemNotFound', [], 'menu'));
            return $this->redirectToRoute('backend.menu.item', ['menuId' => $menuId]);
        }

        $item = $item->toArray(
            $website->getLocale()->getCode(),
            $website->getDefaultLocale()->getCode(),
        );

        $itemDetailsForm = $this->createForm(
            MenuItemDetailsForm::class,
            $item,
            [
                'csrf_protection' => false,
                'menu_id' => $menuId,
                'locale' => $website->getLocale()->getCode(),
                'website_id' => $website->getId(),
            ]
        );
        $itemDetailsForm->handleRequest($request);

        $formDescriptor = $this->contentFormService->buildFormDescriptor(
            $website,
            'menu_item',
            $item,
            [
                'itemDetailsForm' => $itemDetailsForm->createView(),
                'item' => $item,
                'types' => $this->collectMenuTypes(),
                'website_id' => $website->getId(),
                'locale' => $website->getLocale()->getCode(),
            ]
        );
        $formDescriptor->handleRequest($request);

        if ($formDescriptor->isFormValid()) {
            ($updateMenuItem)(new UpdateMenuItemRequest(
                $menuId,
                $id,
                $itemDetailsForm->getData(),
                $formDescriptor->getData(),
                $website->getLocale()->getCode(),
                $website->getDefaultLocale()->getCode(),
                $website->getLocaleCodes()
            ));

            $this->setFlash('success', $this->trans('itemSaved', [], 'menu'));
            return $this->redirectToRoute('backend.menu.item', [ 'menuId' => $menu->getId() ]);
        }

        return $this->view('@backend/menu/item/edit.tpl', [
            'menu'  => $menu,
            'item'  => $item,
            'formDescriptor' => $formDescriptor,
        ]);
    }

    /**
     * @CsrfToken(id="menu.item.delete")
     */
    public function delete(Request $request, DeleteMenuItem $deleteMenuItem, string $menuId): RedirectResponse
    {
        foreach ($request->request->all('ids') as $id) {
            try {
                ($deleteMenuItem)(new DeleteMenuItemRequest($menuId, $id));
            } catch (MenuNotExistsException $e) {
                $this->setFlash('danger', $this->trans('menuNotFound', [], 'menu'));
                return $this->redirectToRoute('backend.menu');
            }
        }

        $this->setFlash('success', $this->trans('selectedItemsWereDeleted', [], 'menu'));
        return $this->redirectToRoute('backend.menu.item.list', [ 'menuId' => $menuId ]);
    }

    private function collectMenuTypes(): array
    {
        $types = [];

        foreach ($this->menuTypeRegistry->all() as $type) {
            if ($type->getSelectorService() === null) {
                continue;
            }

            $types[] = [
                'type'     => $type,
                'selector' => $type->getSelectorService(),
            ];
        }

        return $types;
    }

    private function getEmptyItem(): array
    {
        return [
            'type' => 'simple:homepage',
            'identity' => null,
        ];
    }
}
