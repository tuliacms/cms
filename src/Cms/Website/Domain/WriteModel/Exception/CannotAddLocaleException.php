<?php

declare(strict_types=1);

namespace Tulia\Cms\Website\Domain\WriteModel\Exception;

use Tulia\Cms\Shared\Domain\WriteModel\Exception\AbstractDomainException;
use Tulia\Cms\Website\Domain\WriteModel\Rules\CanAddLocale\CanAddLocaleReasonEnum;

/**
 * @author Adam Banaszkiewicz
 */
final class CannotAddLocaleException extends AbstractDomainException
{
    private function __construct(
        string $message,
        public readonly string $reason,
    ) {
        parent::__construct($message);
    }

    public static function fromReason(CanAddLocaleReasonEnum $reason, string $locale, string $id): self
    {
        return new self(sprintf('Cannot all locale "%s" to website "%s", because: %s', $locale, $id, $reason->value), $reason->value);
    }
}
