<?php

declare(strict_types=1);

namespace Tulia\Cms\Menu\Application\UseCase;

use Tulia\Cms\ContentBuilder\Attributes\Domain\WriteModel\Model\Attribute;
use Tulia\Cms\Menu\Domain\WriteModel\Model\Menu;

/**
 * @author Adam Banaszkiewicz
 */
final class UpdateMenuItem extends AbstractMenuUseCase
{
    /**
     * @param Attribute[] $attributes
     */
    public function __invoke(Menu $menu, string $itemId, array $attributes): void
    {
        $menu->updateItemUsingAttributes(
            $itemId,
            $this->flattenAttributes($attributes),
            $this->removeMenuItemAttributes($attributes)
        );

        $this->update($menu);
    }
}
