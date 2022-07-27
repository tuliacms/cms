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
        private UserRepositoryInterface $userRepository,
        private UserPasswordHasherInterface $passwordHasher,
    ) {
    }

    public function load(ObjectManager $manager)
    {
        $securityUser = new CoreUser('admin@gmail.com', null, ['ROLE_ADMIN']);
        $hashedPassword = $this->passwordHasher->hashPassword($securityUser, 'admin');

        $admin = User::create(
            $this->userRepository->getNextId(),
            'admin@gmail.com',
            $hashedPassword,
            ['ROLE_ADMIN']
        );
        $manager->persist($admin);
        $manager->flush();
    }

    public static function getGroups(): array
    {
        return ['local-database'];
    }
}
