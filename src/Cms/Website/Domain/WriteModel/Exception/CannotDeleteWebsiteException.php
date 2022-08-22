<?php

declare(strict_types=1);

namespace Tulia\Cms\Website\Domain\WriteModel\Exception;

use Tulia\Cms\Shared\Domain\WriteModel\Exception\AbstractDomainException;
use Tulia\Cms\Website\Domain\WriteModel\Rules\CanDeleteWebsite\CanDeleteWebsiteReasonEnum;

/**
 * @author Adam Banaszkiewicz
 */
final class CannotDeleteWebsiteException extends AbstractDomainException
{
    private function __construct(
        string $message,
        public readonly string $reason
    ) {
        parent::__construct($message);
    }

    public static function fromReason(CanDeleteWebsiteReasonEnum $reason, string $id): self
    {
        return new self(sprintf('Cannot delete website "%s", because: %s', $id, $reason->value), $reason->value);
    }
}
