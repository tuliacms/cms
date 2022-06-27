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
    public readonly string $reason;
    public readonly string $purpose;

    public static function fromReason(CanImposePurposeReasonEnum $reason, string $purpose, string $nodeId): self
    {
        $self = new self(sprintf('Cannot impose purpose "%s" to node "%s", because: %s', $purpose, $nodeId, $reason->value));
        $self->reason = $reason->value;
        $self->purpose = $purpose;
        return $self;
    }
}
