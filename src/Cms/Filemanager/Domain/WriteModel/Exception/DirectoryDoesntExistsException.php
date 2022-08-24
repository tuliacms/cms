<?php

declare(strict_types=1);

namespace Tulia\Cms\Filemanager\Domain\WriteModel\Exception;

use Tulia\Cms\Shared\Domain\WriteModel\Exception\AbstractDomainException;

/**
 * @author Adam Banaszkiewicz
 */
final class DirectoryDoesntExistsException extends AbstractDomainException
{
    public static function fromId(string $id): self
    {
        return new self(sprintf('Directory "%s" does not exists.', $id));
    }
}
