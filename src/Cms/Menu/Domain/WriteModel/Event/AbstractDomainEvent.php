<?php

declare(strict_types=1);

namespace Tulia\Cms\Menu\Domain\WriteModel\Event;

use Tulia\Cms\Shared\Domain\WriteModel\Event\AbstractDomainEvent as PlatformDomainEvent;

/**
 * @author Adam Banaszkiewicz
 */
abstract class AbstractDomainEvent extends PlatformDomainEvent
{
    private string $menuId;

    public function __construct(string $menuId)
    {
        $this->menuId = $menuId;
    }

    public function getMenuId(): string
    {
        return $this->menuId;
    }
}
