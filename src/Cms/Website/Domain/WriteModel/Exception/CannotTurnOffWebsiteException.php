<?php

declare(strict_types=1);

namespace Tulia\Cms\Website\Domain\WriteModel\Exception;

use Tulia\Cms\Shared\Domain\WriteModel\Exception\AbstractDomainException;
use Tulia\Cms\Website\Domain\WriteModel\Rules\CanTurnOffWebsite\CanTurnOffWebsiteReasonEnum;

/**
 * @author Adam Banaszkiewicz
 */
final class CannotTurnOffWebsiteException extends AbstractDomainException
{
    private function __construct(
        string $message,
        public readonly string $reason
    ) {
        parent::__construct($message);
    }

    public static function fromReason(CanTurnOffWebsiteReasonEnum $reason, string $id): self
    {
        return new self(sprintf('Cannot turn off website "%s", because: %s', $id, $reason->value), $reason->value);
    }
}
