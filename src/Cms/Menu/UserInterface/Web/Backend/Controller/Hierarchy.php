<?php

declare(strict_types=1);

namespace Tulia\Cms\Menu\UserInterface\Web\Backend\Controller;

use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Tulia\Cms\Menu\Application\UseCase\UpdateItemsHierarchy;
use Tulia\Cms\Menu\Application\UseCase\UpdateItemsHierarchyRequest;
use Tulia\Cms\Menu\Domain\WriteModel\Exception\MenuNotExistsException;
use Tulia\Cms\Menu\Domain\WriteModel\MenuRepositoryInterface;
use Tulia\Cms\Platform\Infrastructure\Framework\Controller\AbstractController;
use Tulia\Cms\Security\Framework\Security\Http\Csrf\Annotation\CsrfToken;
use Tulia\Cms\Platform\Infrastructure\Framework\Routing\Website\WebsiteInterface;

/**
 * @author Adam Banaszkiewicz
 */
class Hierarchy extends AbstractController
{
    public function __construct(
        private MenuRepositoryInterface $repository
    ) {
    }

    public function index(string $menuId, WebsiteInterface $website)
    {
        try {
            $menu = $this->repository->get($menuId);
        } catch (MenuNotExistsException $exception) {
            $this->addFlash('success', $this->trans('menuNotFound', [], 'menu'));
            return $this->redirectToRoute('backend.menu');
        }

        $items = $menu->itemsToArray(
            $website->getLocale()->getCode(),
            $website->getDefaultLocale()->getCode()
        );
        $rootId = $this->findRoot($items);

        return $this->view('@backend/menu/hierarchy/index.tpl', [
            'menu' => $menu,
            'tree' => $this->buildTree($rootId, $items),
            'menuId' => $menuId,
        ]);
    }

    /**
     * @CsrfToken(id="menu_hierarchy")
     */
    public function save(Request $request, UpdateItemsHierarchy $updateItemsHierarchy, string $menuId): RedirectResponse
    {
        try {
            $menu = $this->repository->get($menuId);
        } catch (MenuNotExistsException $exception) {
            $this->addFlash('success', $this->trans('menuNotFound', [], 'menu'));
            return $this->redirectToRoute('backend.menu');
        }

        $hierarchy = (array) $request->request->all('term');

        if (empty($hierarchy)) {
            return $this->redirectToRoute('backend.menu.item.hierarchy', ['menuId' => $menuId]);
        }

        ($updateItemsHierarchy)(new UpdateItemsHierarchyRequest($menuId, $hierarchy));

        $this->addFlash('success', $this->trans('hierarchyUpdated'));
        return $this->redirectToRoute('backend.menu.item.hierarchy', ['menuId' => $menuId]);
    }

    private function buildTree(?string $parentId, array $items): array
    {
        $tree = [];

        foreach ($items as $item) {
            if ($item['parent_id'] === $parentId) {
                $leaf = [
                    'id' => $item['id'],
                    'name' => $item['name'],
                    'position' => $item['position'],
                    'children' => $this->buildTree($item['id'], $items),
                ];

                $tree[] = $leaf;
            }
        }

        usort($tree, function (array $a, array $b) {
            return $a['position'] <=> $b['position'];
        });

        return $tree;
    }

    private function findRoot(array $items): string
    {
        foreach ($items as $item) {
            if ($item['is_root']) {
                return $item['id'];
            }
        }

        throw new \RuntimeException('Missing root item in elements.');
    }
}
