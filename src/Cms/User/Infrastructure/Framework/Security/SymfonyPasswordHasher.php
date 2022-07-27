<?php

declare(strict_types=1);

namespace Tulia\Cms\User\Infrastructure\Framework\Security;

use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Tulia\Cms\Security\Framework\Security\Core\User\UserProvider;
use Tulia\Cms\User\Domain\WriteModel\Service\PasswordHasherInterface;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;

/**
 * @author Adam Banaszkiewicz
 */
final class SymfonyPasswordHasher implements PasswordHasherInterface
{
    public function __construct(
        private UserProvider $provider,
        private UserPasswordHasherInterface $passwordHasher
    ) {
    }

    public function hashPassword(string $username, string $password): string
    {
        /** @var PasswordAuthenticatedUserInterface $user */
        $user = $this->provider->loadUserByIdentifier($username);
        return $this->passwordHasher->hashPassword($user, $password);
    }
}
