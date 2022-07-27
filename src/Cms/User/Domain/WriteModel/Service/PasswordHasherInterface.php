<?php

declare(strict_types=1);

namespace Tulia\Cms\User\Domain\WriteModel\Service;

/**
 * @author Adam Banaszkiewicz
 */
interface PasswordHasherInterface
{
    public function hashPassword(string $username, string $password): string;
}
