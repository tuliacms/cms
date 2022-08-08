<?php

declare(strict_types=1);

namespace Tulia\Cms\Menu\Domain\WriteModel;

use Tulia\Cms\Menu\Domain\WriteModel\MewModel\Menu;

/**
 * @author Adam Banaszkiewicz
 */
interface MenuRepositoryInterface
{
    public function createNewMenu(string $name): Menu;

    public function get(string $id): Menu;

    public function save(Menu $menu): void;

    public function delete(Menu $menu): void;
}
