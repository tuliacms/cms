<?php

declare(strict_types=1);

namespace Tulia\Cms\Menu\UserInterface\Web\Backend\BackendMenu;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\RequestStack;
use Tulia\Cms\BackendMenu\Builder\BuilderInterface;
use Tulia\Cms\BackendMenu\Builder\Helper\BuilderHelperInterface;
use Tulia\Cms\BackendMenu\Builder\Registry\ItemRegistryInterface;
use Tulia\Cms\Menu\Domain\ReadModel\Finder\MenuFinderInterface;
use Tulia\Cms\Menu\Domain\ReadModel\Finder\MenuFinderScopeEnum;
use Tulia\Cms\Menu\Domain\WriteModel\Event\MenuCreated;
use Tulia\Cms\Menu\Domain\WriteModel\Event\MenuDeleted;
use Tulia\Cms\Menu\Domain\WriteModel\Event\MenuUpdated;

/**
 * @author Adam Banaszkiewicz
 */
class MenuMenuBuilder implements BuilderInterface, EventSubscriberInterface
{
    private const SESSION_KEY = '_cms_app_backendmenu_menu_cache_%s';

    public function __construct(
        private readonly BuilderHelperInterface $helper,
        private readonly MenuFinderInterface $menuFinder,
        private readonly RequestStack $requestStack,
    ) {
    }

    public static function getSubscribedEvents(): array
    {
        return [
            MenuCreated::class => 'clearCache',
            MenuUpdated::class => 'clearCache',
            MenuDeleted::class => 'clearCache',
        ];
    }

    /**
     * Event listener method, which clears cache when menu is created/updated/deleted.
     */
    public function clearCache(): void
    {
        $request = $this->requestStack->getMainRequest();

        if ($request && $request->hasSession() && $request->getSession()->has($this->getCachekey())) {
            $request->getSession()->remove($this->getCachekey());
        }
    }

    public function build(ItemRegistryInterface $registry, string $websiteId, string $locale): void
    {
        $root = 'menu';

        $registry->add($root, [
            'label'    => $this->helper->trans('menu'),
            'link'     => '#',
            'icon'     => 'fas fa-bars',
            'priority' => 2000,
            'parent'   => 'section_contents',
        ]);

        $registry->add($root . '_list', [
            'label'    => $this->helper->trans('menuList'),
            'link'     => $this->helper->generateUrl('backend.menu'),
            'parent'   => $root,
            'priority' => 5000,
        ]);

        foreach ($this->getMenus($websiteId, $locale) as $menu) {
            $registry->add($root . '_menu_' . $menu['id'], [
                'label'    => $menu['name'],
                'link'     => $this->helper->generateUrl('backend.menu.item', ['menuId' => $menu['id']]),
                'parent'   => $root,
                'priority' => 3000,
            ]);
        }
    }

    private function getMenus(string $websiteId, string $locale): array
    {
        $request = $this->requestStack->getMainRequest();

        if ($request && $request->hasSession() && $request->getSession()->has($this->getCachekey())) {
            return $request->getSession()->get($this->getCachekey());
        }

        $source = $this->menuFinder->find([
            'website_id' => $websiteId,
            'locale' => $locale,
       ], MenuFinderScopeEnum::INTERNAL);

        $menus = [];

        foreach ($source as $menu) {
            $menus[] = [
                'id'   => $menu->getId(),
                'name' => $menu->getName(),
            ];
        }

        if ($request && $request->hasSession()) {
            $request->getSession()->set($this->getCachekey(), $menus);
        }

        return $menus;
    }

    private function getCachekey(): string
    {
        return self::SESSION_KEY;
    }
}
