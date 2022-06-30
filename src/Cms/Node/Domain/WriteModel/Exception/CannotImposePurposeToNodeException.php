<?php

declare(strict_types=1);

namespace Tulia\Cms\Node\Domain\WriteModel\Exception;

use Tulia\Cms\Node\Domain\WriteModel\Rules\CanAddPurpose\CanImposePurposeReasonEnum;
use Tulia\Cms\Shared\Domain\WriteModel\Exception\AbstractDomainException;

/**
 * @author Adam Banaszkiewicz
 */
final class CannotImposePurposeToNodeException extends AbstractDomainException
{
    private function __construct(
        string $message,
        public readonly string $reason,
        public readonly string $purpose,
    ) {
        parent::__construct($message);
    }

    public static function fromReason(CanImposePurposeReasonEnum $reason, string $purpose, string $nodeId): self
    {
        return new self(sprintf('Cannot impose purpose "%s" to node "%s", because: %s', $purpose, $nodeId, $reason->value), $reason->value, $purpose);
    }
}
