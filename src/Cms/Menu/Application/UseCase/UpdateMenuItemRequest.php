<?php

declare(strict_types=1);

namespace Tulia\Cms\Menu\Application\UseCase;

use Tulia\Cms\Shared\Application\UseCase\RequestInterface;

/**
 * @author Adam Banaszkiewicz
 */
final class UpdateMenuItemRequest implements RequestInterface
{
    public function __construct(
        public readonly string $menuId,
        public readonly string $itemId,
        public readonly string $locale,
        public readonly array $details,
        public readonly array $attributes
    ) {
    }
}
