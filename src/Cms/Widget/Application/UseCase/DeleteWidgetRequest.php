<?php

declare(strict_types=1);

namespace Tulia\Cms\Widget\Application\UseCase;

use Tulia\Cms\Shared\Application\UseCase\RequestInterface;

/**
 * @author Adam Banaszkiewicz
 */
final class DeleteWidgetRequest implements RequestInterface
{
    public function __construct(
        public readonly string $id,
    ) {
    }
}
