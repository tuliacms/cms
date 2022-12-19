<?php

declare(strict_types=1);

namespace Tulia\Cms\Taxonomy\Domain\WriteModel\Exception;

use Tulia\Cms\Shared\Domain\WriteModel\Exception\AbstractDomainException;

/**
 * @author Adam Banaszkiewicz
 */
class TermNotFoundException extends AbstractDomainException
{
    public static function fromId(string $id): self
    {
        return new self(sprintf('Term %s does not exists.', $id));
    }
}
