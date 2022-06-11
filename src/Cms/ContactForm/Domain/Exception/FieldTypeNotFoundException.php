<?php

declare(strict_types=1);

namespace Tulia\Cms\ContactForm\Domain\Exception;

use Tulia\Cms\Shared\Domain\WriteModel\Exception\AbstractDomainException;

/**
 * @author Adam Banaszkiewicz
 */
class FieldTypeNotFoundException extends AbstractDomainException
{
    public static function fromType(string $type): self
    {
        return new self(sprintf('Field type "%s" not found.', $type));
    }
}
