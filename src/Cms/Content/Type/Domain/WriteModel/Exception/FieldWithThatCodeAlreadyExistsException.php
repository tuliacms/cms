<?php

declare(strict_types=1);

namespace Tulia\Cms\Content\Type\Domain\WriteModel\Exception;

use Tulia\Cms\Shared\Domain\WriteModel\Exception\AbstractDomainException;

/**
 * @author Adam Banaszkiewicz
 */
final class FieldWithThatCodeAlreadyExistsException extends AbstractDomainException
{
    public static function fromCode(string $code): self
    {
        return new self(sprintf('Field with code "%s" already exists in this content type.', $code));
    }
}
