<?php

declare(strict_types=1);

namespace Tulia\Cms\Menu\Domain\WriteModel;

use Tulia\Cms\Menu\Domain\WriteModel\Model\Item;
use Tulia\Cms\Menu\Domain\WriteModel\Model\Menu;
use Tulia\Cms\Menu\Domain\WriteModel\MewModel\Menu as NewMenu;

/**
 * @author Adam Banaszkiewicz
 */
interface MenuRepositoryInterface
{
    public function createNewMenu(string $name): NewMenu;

    public function createNewItem(Menu $menu): Item;

    public function get(string $id): NewMenu;

    public function save(NewMenu $menu): void;

    public function delete(NewMenu $menu): void;
}
