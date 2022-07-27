<?php

declare(strict_types=1);

namespace Tulia\Cms\User\Domain\WriteModel\Rules\CanDeleteUser;

/**
 * @author Adam Banaszkiewicz
 */
enum CanDeleteUserReasonEnum: string
{
    case CannotDeleteYourself = 'Cannot delete Yourself';
    case OK = 'OK';
}
