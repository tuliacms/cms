<?php

declare(strict_types=1);

namespace Tulia\Cms\User\Domain\WriteModel\Rules\CanDeleteUser;

/**
 * @author Adam Banaszkiewicz
 */
interface CanDeleteUserInterface
{
    public function decide(
        string $userId
    ): CanDeleteUserReasonEnum;
}
