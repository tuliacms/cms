<?php

declare(strict_types=1);

namespace Tulia\Cms\Menu\UserInterface\Web\Backend\Controller;

use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Tulia\Cms\Menu\Application\UseCase\CreateMenu;
use Tulia\Cms\Menu\Application\UseCase\CreateMenuRequest;
use Tulia\Cms\Menu\Application\UseCase\DeleteMenu;
use Tulia\Cms\Menu\Application\UseCase\DeleteMenuRequest;
use Tulia\Cms\Menu\Application\UseCase\UpdateMenu;
use Tulia\Cms\Menu\Application\UseCase\UpdateMenuRequest;
use Tulia\Cms\Menu\Domain\ReadModel\Datatable\MenuDatatableFinderInterface;
use Tulia\Cms\Menu\Domain\WriteModel\Exception\MenuNotExistsException;
use Tulia\Cms\Platform\Infrastructure\Framework\Controller\AbstractController;
use Tulia\Cms\Security\Framework\Security\Http\Csrf\Annotation\CsrfToken;
use Tulia\Component\Datatable\DatatableFactory;
use Tulia\Component\Templating\ViewInterface;

/**
 * @author Adam Banaszkiewicz
 */
class Menu extends AbstractController
{
    public function __construct(
        private DatatableFactory $factory,
        private MenuDatatableFinderInterface $finder
    ) {
    }

    public function list(Request $request): ViewInterface
    {
        return $this->view('@backend/menu/menu/list.tpl', [
            'datatable' => $this->factory->create($this->finder, $request),
        ]);
    }

    public function datatable(Request $request): JsonResponse
    {
        return $this->factory->create($this->finder, $request)->generateResponse();
    }

    /**
     * @CsrfToken(id="menu.create")
     */
    public function create(
        Request $request,
        CreateMenu $createMenu
    ): RedirectResponse {
        ($createMenu)(new CreateMenuRequest($request->request->get('name')));

        $this->setFlash('success', $this->trans('menuCreated', [], 'menu'));
        return $this->redirectToRoute('backend.menu');
    }

    /**
     * @throws NotFoundHttpException
     * @CsrfToken(id="menu.edit")
     */
    public function edit(Request $request, UpdateMenu $updateMenu): RedirectResponse
    {
        try {
            ($updateMenu)(new UpdateMenuRequest(
                $request->request->get('id'),
                $request->request->get('name')
            ));
        } catch (MenuNotExistsException $e) {
            $this->setFlash('success', $this->trans('menuNotFound', [], 'menu'));
            return $this->redirectToRoute('backend.menu');
        }

        $this->setFlash('success', $this->trans('menuUpdated', [], 'menu'));
        return $this->redirectToRoute('backend.menu');
    }

    /**
     * @CsrfToken(id="menu.delete")
     */
    public function delete(Request $request, DeleteMenu $deleteMenu): RedirectResponse
    {
        foreach ($request->request->all('ids') as $id) {
            try {
                ($deleteMenu)(new DeleteMenuRequest($id));
            } catch (MenuNotExistsException $e) {
                // Cannot delete not existent menu :)
                continue;
            }
        }

        $this->setFlash('success', $this->trans('selectedMenusWereDeleted', [], 'menu'));
        return $this->redirectToRoute('backend.menu');
    }
}
