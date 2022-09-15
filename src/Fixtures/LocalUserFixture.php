<?php

declare(strict_types=1);

namespace Tulia\Cms\Fixtures;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Bundle\FixturesBundle\FixtureGroupInterface;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Tulia\Cms\Security\Framework\Security\Core\User\User as CoreUser;
use Tulia\Cms\User\Domain\WriteModel\Model\User;
use Tulia\Cms\User\Domain\WriteModel\UserRepositoryInterface;

/**
 * @author Adam Banaszkiewicz
 */
final class LocalUserFixture extends Fixture implements FixtureGroupInterface
{
    public function __construct(
        private readonly UserRepositoryInterface $userRepository,
        private readonly UserPasswordHasherInterface $passwordHasher,
    ) {
    }

    public function load(ObjectManager $manager)
    {
        $this->createUser($manager, 'admin@gmail.com', 'admin', 'en_US');
        $this->createUser($manager, 'admin_pl@gmail.com', 'admin', 'pl_PL');
    }

    public static function getGroups(): array
    {
        return ['local-database'];
    }

    private function createUser(ObjectManager $manager, string $username, string $password, string $locale): void
    {
        $securityUser = new CoreUser($username, null, ['ROLE_ADMIN']);
        $hashedPassword = $this->passwordHasher->hashPassword($securityUser, $password);

        $admin = User::create(
            $this->userRepository->getNextId(),
            $username,
            $hashedPassword,
            ['ROLE_ADMIN'],
            locale: $locale,
        );
        $manager->persist($admin);
        $manager->flush();
    }
}
