<?php

declare(strict_types=1);

namespace Tulia\Cms\User\Domain\WriteModel\Event;

/**
 * @author Adam Banaszkiewicz
 */
class RoleWasGiven extends AbstractUserDomainEvent
{
    private string $role;

    public function __construct(string $userId, string $role)
    {
        parent::__construct($userId);

        $this->role = $role;
    }

    public function getRole(): string
    {
        return $this->role;
    }
}
