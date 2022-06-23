<?php

declare(strict_types=1);

namespace Tulia\Cms\Node\Domain\WriteModel\Model\ValueObject;

/**
 * @author Adam Banaszkiewicz
 */
final class Author
{
    public function __construct(
        private string $id
    ) {
    }

    public function getId(): string
    {
        return $this->id;
    }
}
