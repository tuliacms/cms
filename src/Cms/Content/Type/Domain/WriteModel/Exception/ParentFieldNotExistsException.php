<?php

declare(strict_types=1);

namespace Tulia\Cms\Content\Type\Domain\WriteModel\Exception;

use Tulia\Cms\Shared\Domain\WriteModel\Exception\AbstractDomainException;

/**
 * @author Adam Banaszkiewicz
 */
final class ParentFieldNotExistsException extends AbstractDomainException
{
    public static function fromName(string $parentName): self
    {
        return new self(sprintf('Parent field with code "%s" not exists.', $parentName));
    }
}
