<?php

declare(strict_types=1);

namespace Tulia\Cms\Menu\UserInterface\Web\Backend\Controller;

use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Tulia\Cms\Content\Type\Infrastructure\Framework\Form\Service\ContentFormService;
use Tulia\Cms\Content\Type\UserInterface\Web\Backend\Form\FormAttributesExtractor;
use Tulia\Cms\Menu\Application\UseCase\CreateMenuItem;
use Tulia\Cms\Menu\Application\UseCase\CreateMenuItemRequest;
use Tulia\Cms\Menu\Application\UseCase\DeleteMenuItem;
use Tulia\Cms\Menu\Application\UseCase\DeleteMenuItemRequest;
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
use Tulia\Cms\Platform\Infrastructure\Framework\Routing\Website\WebsiteInterface;
use Tulia\Component\Templating\ViewInterface;

/**
 * @author Adam Banaszkiewicz
 */
class MenuItem extends AbstractController
{
    public function __construct(
        private readonly MenuRepositoryInterface $repository,
        private readonly RegistryInterface $menuTypeRegistry,
        private readonly DatatableFactory $factory,
        private readonly ItemDatatableFinderInterface $finder,
        private readonly ContentFormService $contentFormService,
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
            $this->addFlash('danger', $this->trans('menuNotFound', [], 'menu'));
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
     * @CsrfToken(id="menu_item_details_form")
     */
    public function create(
        Request $request,
        string $menuId,
        CreateMenuItem $createMenuItem,
        WebsiteInterface $website,
        FormAttributesExtractor $extractor,
    ) {
        if (!$website->isDefaultLocale()) {
            $this->addFlash('info', $this->trans('youHaveBeenRedirectedToDefaultLocaleDueToCreationMultilingualElement'));
            return $this->redirectToRoute('backend.menu.item.create', ['menuId' => $menuId, '_locale' => $website->getDefaultLocale()->getCode()]);
        }

        try {
            $menu = $this->repository->get($menuId);
        } catch (MenuNotExistsException $e) {
            $this->addFlash('danger', $this->trans('menuNotFound', [], 'menu'));
            return $this->redirectToRoute('backend.menu');
        }

        $form = $this->createForm(
            MenuItemDetailsForm::class,
            [],
            [
                'menu_id' => $menuId,
                'website' => $website,
                'content_type' => 'menu_item',
                'context' => [
                    'types' => $this->collectMenuTypes(),
                    'item' => $this->getEmptyItem(),
                    'website' => $website,
                ],
            ]
        );
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            ($createMenuItem)(new CreateMenuItemRequest(
                $menuId,
                $extractor->extractData($form, 'menu_item'),
                $website->getLocale()->getCode(),
                $website->getLocaleCodes(),
            ));

            $this->addFlash('success', $this->trans('itemSaved', [], 'menu'));
            return $this->redirectToRoute('backend.menu.item', [ 'menuId' => $menu->getId() ]);
        }

        return $this->view('@backend/menu/item/create.tpl', [
            'menu'  => $menu,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @return RedirectResponse|ViewInterface
     * @CsrfToken(id="menu_item_details_form")
     */
    public function edit(
        Request $request,
        string $menuId,
        string $id,
        UpdateMenuItem $updateMenuItem,
        WebsiteInterface $website,
        FormAttributesExtractor $extractor,
    ) {
        try {
            $menu = $this->repository->get($menuId);
            $item = $menu->getItem($id);
        } catch (MenuNotExistsException $e) {
            $this->addFlash('danger', $this->trans('menuNotFound', [], 'menu'));
            return $this->redirectToRoute('backend.menu');
        } catch (MenuItemDoesntExistsException $e) {
            $this->addFlash('danger', $this->trans('menuItemNotFound', [], 'menu'));
            return $this->redirectToRoute('backend.menu.item', ['menuId' => $menuId]);
        }

        $item = $item->toArray(
            $website->getLocale()->getCode(),
            $website->getDefaultLocale()->getCode(),
        );

        $form = $this->createForm(
            MenuItemDetailsForm::class,
            $item,
            [
                'menu_id' => $menuId,
                'website' => $website,
                'content_type' => 'menu_item',
                'context' => [
                    'types' => $this->collectMenuTypes(),
                    'item' => $item,
                    'website' => $website,
                ],
            ]
        );
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            ($updateMenuItem)(new UpdateMenuItemRequest(
                $menuId,
                $id,
                $extractor->extractData($form, 'menu_item'),
                $website->getLocale()->getCode(),
                $website->getDefaultLocale()->getCode(),
                $website->getLocaleCodes()
            ));

            $this->addFlash('success', $this->trans('itemSaved', [], 'menu'));
            return $this->redirectToRoute('backend.menu.item', [ 'menuId' => $menu->getId() ]);
        }

        return $this->view('@backend/menu/item/edit.tpl', [
            'menu'  => $menu,
            'item'  => $item,
            'form' => $form->createView(),
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
                $this->addFlash('danger', $this->trans('menuNotFound', [], 'menu'));
                return $this->redirectToRoute('backend.menu');
            }
        }

        $this->addFlash('success', $this->trans('selectedItemsWereDeleted', [], 'menu'));
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
