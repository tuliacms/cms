<?php

declare(strict_types=1);

namespace Tulia\Cms\Menu\Domain\WriteModel\Exception;

use Tulia\Cms\Shared\Domain\WriteModel\Exception\AbstractDomainException;

/**
 * @author Adam Banaszkiewicz
 */
final class MenuNotExistsException extends AbstractDomainException
{
    public static function fromId(string $id): self
    {
        return new self(sprintf('Menu %s not exists', $id));
    }
}
