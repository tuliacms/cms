<?php

declare(strict_types=1);

namespace Tulia\Cms\Content\Type\Domain\WriteModel\Exception;

/**
 * @author Adam Banaszkiewicz
 */
final class GroupWithCodeExistsException extends \DomainException
{
    public static function fromCode(string $code): self
    {
        return new self(sprintf('Fields group with code %s already exists in aggregate.', $code));
    }
}
