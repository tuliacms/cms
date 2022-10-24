<?php

declare(strict_types=1);

namespace Tulia\Cms\Website\Domain\WriteModel\Exception;

use Tulia\Cms\Shared\Domain\WriteModel\Exception\AbstractDomainException;
use Tulia\Cms\Website\Domain\WriteModel\Rules\CanDeleteLocale\CanDeleteLocaleReasonEnum;

/**
 * @author Adam Banaszkiewicz
 */
final class CannotDeleteLocaleException extends AbstractDomainException
{
    private function __construct(
        string $message,
        public readonly string $reason,
    ) {
        parent::__construct($message);
    }

    public static function fromReason(CanDeleteLocaleReasonEnum $reason, string $code, string $id): self
    {
        return new self(sprintf('Cannot delete locale "%s" of website "%s", because: %s', $code, $id, $reason->value), $reason->value);
    }
}
