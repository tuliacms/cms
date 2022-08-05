<?php

declare(strict_types=1);

namespace Tulia\Cms\Menu\Domain\WriteModel\Exception;

use Tulia\Cms\Shared\Domain\WriteModel\Exception\AbstractDomainException;

/**
 * @author Adam Banaszkiewicz
 */
final class MenuItemDoesntExistsException extends AbstractDomainException
{
    public static function fromId(string $menuId, string $itemId): self
    {
        return new self(sprintf('Menu item %s does not exists in menu %s', $itemId, $menuId));
    }
}
