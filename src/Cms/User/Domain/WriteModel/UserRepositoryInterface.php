<?php

declare(strict_types=1);

namespace Tulia\Cms\User\Domain\WriteModel;

use Tulia\Cms\User\Domain\WriteModel\Model\User;

/**
 * @author Adam Banaszkiewicz
 */
interface UserRepositoryInterface
{
    public function get(string $id): User;

    public function save(User $user): void;

    public function delete(User $user): void;

    public function getNextId(): string;
}
