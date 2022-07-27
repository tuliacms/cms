<?php

declare(strict_types=1);

namespace Tulia\Cms\Node\Domain\WriteModel\Model;

/**
 * @author Adam Banaszkiewicz
 * @final
 */
class Purpose
{
    private string $id;

    public function __construct(
        private Node $node,
        private string $purpose
    ) {
    }

    public function __toString(): string
    {
        return $this->purpose;
    }

    public function is(string $purposeCode): bool
    {
        return $this->purpose === $purposeCode;
    }
}
