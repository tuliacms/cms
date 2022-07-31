<?php

declare(strict_types=1);

namespace Tulia\Cms\User\Domain\WriteModel\Event;

use Tulia\Cms\Shared\Domain\WriteModel\Event\AbstractDomainEvent;

/**
 * @author Adam Banaszkiewicz
 */
class UserUpdated extends AbstractDomainEvent
{
    public function __construct(
        public readonly string $id,
        public readonly string $email,
        public readonly ?string $name,
    ) {
    }
}
