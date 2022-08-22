<?php

declare(strict_types=1);

namespace Tulia\Cms\Options\Domain\WriteModel\Exception;

use Tulia\Cms\Shared\Domain\WriteModel\Exception\AbstractDomainException;

/**
 * @author Adam Banaszkiewicz
 */
class OptionNotFoundException extends AbstractDomainException
{
    public static function fromName(string $name): self
    {
        return new self(sprintf('Option "%s" not found.', $name));
    }
}
