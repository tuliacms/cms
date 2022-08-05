<?php

declare(strict_types=1);

namespace Tulia\Cms\Menu\Application\UseCase;

use Tulia\Cms\Shared\Application\UseCase\RequestInterface;

/**
 * @author Adam Banaszkiewicz
 */
final class UpdateMenuRequest implements RequestInterface
{
    public function __construct(
        public readonly string $id,
        public readonly string $name,
    ) {
    }
}
