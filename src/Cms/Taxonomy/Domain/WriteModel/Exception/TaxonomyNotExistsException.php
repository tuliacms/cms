<?php

declare(strict_types=1);

namespace Tulia\Cms\Taxonomy\Domain\WriteModel\Exception;

use Tulia\Cms\Shared\Domain\WriteModel\Exception\AbstractDomainException;

/**
 * @author Adam Banaszkiewicz
 */
final class TaxonomyNotExistsException extends AbstractDomainException
{
    public static function fromType(string $type): self
    {
        return new self(sprintf('Taxonomy "%s" does not exists.', $type));
    }
}
