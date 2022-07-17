<?php

declare(strict_types=1);

namespace Tulia\Cms\Node\Domain\WriteModel\Exception;

use Tulia\Cms\Shared\Domain\WriteModel\Exception\AbstractDomainException;

/**
 * @author Adam Banaszkiewicz
 */
final class NodeDoesntExistsException extends AbstractDomainException
{
    private function __construct(string $message)
    {
        parent::__construct($message);
    }

    public static function fromId(string $id): self
    {
        return new self(sprintf('Node "%s" does not exists.', $id));
    }
}
