<?php

declare(strict_types=1);

namespace Tulia\Cms\Content\Type\Domain\WriteModel\Event;

use Tulia\Cms\Shared\Domain\WriteModel\Event\DomainEvent;

/**
 * @author Adam Banaszkiewicz
 */
final class FieldsGroupsSorted extends DomainEvent
{
    private string $contentType;
    /** @var string[] */
    private array $newPositions;

    /**
     * @param string[] $newPositions
     */
    public function __construct(
        string $contentType,
        array $newPositions
    ) {
        $this->contentType = $contentType;
        $this->newPositions = $newPositions;
    }

    public function getContentType(): string
    {
        return $this->contentType;
    }

    public function getNewPositions(): array
    {
        return $this->newPositions;
    }
}
