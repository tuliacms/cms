<?php

declare(strict_types=1);

namespace Tulia\Cms\User\Domain\WriteModel\Event;

/**
 * @author Adam Banaszkiewicz
 */
class UsernameChanged extends AbstractUserDomainEvent
{
    private string $username;

    public function __construct(string $userId, string $username)
    {
        parent::__construct($userId);

        $this->username = $username;
    }

    /**
     * @return string
     */
    public function getUsername(): string
    {
        return $this->username;
    }
}
