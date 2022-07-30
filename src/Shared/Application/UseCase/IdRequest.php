<?php

declare(strict_types=1);

namespace Tulia\Cms\Shared\Application\UseCase;

/**
 * @author Adam Banaszkiewicz
 */
final class IdRequest implements RequestInterface
{
    public function __construct(
        public readonly string $id
    ) {
    }
}
