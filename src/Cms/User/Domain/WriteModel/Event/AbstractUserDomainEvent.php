<?php

declare(strict_types=1);

namespace Tulia\Cms\User\Domain\WriteModel\Event;

use Tulia\Cms\Shared\Domain\WriteModel\Event\AbstractDomainEvent as PlatformDomainEvent;

/**
 * @author Adam Banaszkiewicz
 */
abstract class AbstractUserDomainEvent extends PlatformDomainEvent
{
    public function __construct(
        public readonly  string $userId
    ) {
    }
}
