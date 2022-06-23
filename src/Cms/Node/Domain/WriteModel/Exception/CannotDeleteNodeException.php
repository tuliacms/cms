<?php

declare(strict_types=1);

namespace Tulia\Cms\Node\Domain\WriteModel\Exception;

use Tulia\Cms\Node\Domain\WriteModel\Rules\CanDeleteNode\CanDeleteNodeReasonEnum;
use Tulia\Cms\Shared\Domain\WriteModel\Exception\AbstractDomainException;

/**
 * @author Adam Banaszkiewicz
 */
final class CannotDeleteNodeException extends AbstractDomainException
{
    public readonly string $reason;
    public readonly string $title;

    public static function fromReason(CanDeleteNodeReasonEnum $reason, string $nodeId, string $title): self
    {
        $self = new self(sprintf('Cannot delete node "%s", because: %s', $nodeId, $reason->value));
        $self->reason = $reason->value;
        $self->title = $title;
        return $self;
    }
}
