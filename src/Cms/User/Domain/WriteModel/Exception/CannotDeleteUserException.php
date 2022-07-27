<?php

declare(strict_types=1);

namespace Tulia\Cms\User\Domain\WriteModel\Exception;

use Tulia\Cms\Shared\Domain\WriteModel\Exception\AbstractDomainException;
use Tulia\Cms\User\Domain\WriteModel\Rules\CanDeleteUser\CanDeleteUserReasonEnum;

/**
 * @author Adam Banaszkiewicz
 */
class CannotDeleteUserException extends AbstractDomainException
{
    public readonly CanDeleteUserReasonEnum $reason;

    public function __construct(string $message, CanDeleteUserReasonEnum $reason)
    {
        parent::__construct($message);
        $this->reason = $reason;
    }

    public static function fromReason(CanDeleteUserReasonEnum $reason, string $id): self
    {
        return new self(sprintf('Cannot delete user %s, because: %s.', $id, $reason->value), $reason);
    }
}
