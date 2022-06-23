<?php

declare(strict_types=1);

namespace Tulia\Cms\Node\Domain\WriteModel\Exception;

/**
 * @author Adam Banaszkiewicz
 */
class NodePurposeNotFoundException extends \Exception
{
    public static function fromName(string $name): self
    {
        return new self(sprintf('Node Purpose "%s" not found.', $name));
    }
}
