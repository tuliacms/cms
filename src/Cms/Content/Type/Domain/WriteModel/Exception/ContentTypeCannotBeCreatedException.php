<?php

declare(strict_types=1);

namespace Tulia\Cms\Content\Type\Domain\WriteModel\Exception;

use Tulia\Cms\Content\Type\Domain\WriteModel\Rules\CanCreateContentTypeReason;
use Tulia\Cms\Shared\Domain\WriteModel\Exception\AbstractDomainException;

/**
 * @author Adam Banaszkiewicz
 */
final class ContentTypeCannotBeCreatedException extends AbstractDomainException
{
    public readonly CanCreateContentTypeReason $reason;

    public static function fromReason(CanCreateContentTypeReason $reason): self
    {
        $self = new self(sprintf('Content type cannot be created because: %s.', $reason->value));
        $self->reason = $reason;

        return $self;
    }
}
