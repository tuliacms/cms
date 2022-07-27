<?php

declare(strict_types=1);

namespace Tulia\Cms\User\Infrastructure\Persistence\Doctrine\Orm;

use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\Uid\Uuid;
use Tulia\Cms\User\Domain\WriteModel\Exception\UserNotFoundException;
use Tulia\Cms\User\Domain\WriteModel\Model\User;
use Tulia\Cms\User\Domain\WriteModel\UserRepositoryInterface;

/**
 * @author Adam Banaszkiewicz
 */
final class OrmUserRepository extends ServiceEntityRepository implements UserRepositoryInterface
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, User::class);
    }

    public function get(string $id): User
    {
        $user = $this->find($id);

        if (!$user) {
            throw new UserNotFoundException(sprintf('User with ID %s not found.', $id));
        }

        return $user;
    }

    public function save(User $user): void
    {
        $this->_em->persist($user);
        $this->_em->flush();
    }

    public function delete(User $user): void
    {
        $this->_em->remove($user);
        $this->_em->flush();
    }

    public function getNextId(): string
    {
        return (string) Uuid::v4();
    }
}
