<?php

declare(strict_types=1);

namespace Tulia\Cms\User\Application\UseCase;

use Tulia\Cms\Shared\Application\UseCase\RequestInterface;

/**
 * @author Adam Banaszkiewicz
 */
final class DeleteUserRequest implements RequestInterface
{
    public function __construct(
        public readonly string $id
    ) {
    }
}
