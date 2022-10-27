<?php

declare(strict_types=1);

namespace Tulia\Cms\Security\Application\Service;

use Symfony\Component\PasswordHasher\Hasher\PasswordHasherFactoryInterface;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Tulia\Cms\User\Application\Service\AuthenticatedUserProviderInterface;
use Tulia\Cms\User\Domain\ReadModel\Finder\UserFinderInterface;
use Tulia\Cms\User\Domain\ReadModel\Finder\UserFinderScopeEnum;

/**
 * @author Adam Banaszkiewicz
 */
final class AuthenticatedUserPasswordValidator
{
    public function __construct(
        private readonly PasswordHasherFactoryInterface $hasherFactory,
        private readonly UserFinderInterface $finder,
        private readonly AuthenticatedUserProviderInterface $provider,
        private readonly TokenStorageInterface $tokenStorage,
    ) {
    }

    public function isValid(string $password): bool
    {
        $coreUser = $this->tokenStorage->getToken()?->getUser();

        if (!$coreUser) {
            return false;
        }

        $user = $this->finder->find(['email' => $coreUser->getUserIdentifier()], UserFinderScopeEnum::INTERNAL)->first();

        return $this->hasherFactory->getPasswordHasher($coreUser)->verify($user->getPassword(), $password);
    }
}
