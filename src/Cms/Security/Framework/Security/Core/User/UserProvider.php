<?php

declare(strict_types=1);

namespace Tulia\Cms\Security\Framework\Security\Core\User;

use Doctrine\DBAL\Connection;
use Symfony\Component\Security\Core\Exception\UnsupportedUserException;
use Symfony\Component\Security\Core\Exception\UserNotFoundException;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\PasswordUpgraderInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\UserProviderInterface;
use Throwable;

/**
 * @author Adam Banaszkiewicz
 */
class UserProvider implements UserProviderInterface, PasswordUpgraderInterface
{
    public function __construct(
        private Connection $connection
    ) {
    }

    public function loadUserByIdentifier(string $identifier): UserInterface
    {
        return $this->getUser($identifier);
    }

    public function loadUserByUsername(string $username): UserInterface
    {
        return $this->getUser($username);
    }

    public function refreshUser(UserInterface $user): UserInterface
    {
        if (! $user instanceof User) {
            throw new UnsupportedUserException(sprintf('Invalid user class "%s".', get_class($user)));
        }

        $storedUser = $this->getUser($user->getUsername());

        return $user;
    }

    public function supportsClass(string $class): bool
    {
        return User::class === $class || is_subclass_of($class, User::class);
    }

    public function upgradePassword(PasswordAuthenticatedUserInterface $user, string $newHashedPassword): void
    {
        // TODO: when encoded passwords are in use, this method should:
        // 1. persist the new password in the user storage
        // 2. update the $user object with $user->setPassword($newEncodedPassword);
    }

    private function getUser(string $email): UserInterface
    {
        $result = $this->connection->fetchAllAssociative('SELECT * FROM #__user WHERE email = :email LIMIT 1', [
            'email' => $email
        ]);

        if ($result === []) {
            $ex = new UserNotFoundException(sprintf('Email "%s" does not exist.', $email));
            $ex->setUserIdentifier($email);

            throw $ex;
        }

        try {
            $roles = json_decode($result[0]['roles']);

            if (! \is_array($roles)) {
                $roles = [];
            }
        } catch (Throwable $e) {
            $roles = [];
        }

        $user = new User(
            $result[0]['email'],
            $result[0]['password'],
            $roles,
            null // @todo Make salts for users
        );

        return $user;
    }
}
