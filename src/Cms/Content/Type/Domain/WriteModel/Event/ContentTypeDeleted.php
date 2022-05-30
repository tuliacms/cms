<?php

declare(strict_types=1);

namespace Tulia\Cms\Content\Type\Domain\WriteModel\Event;

use Tulia\Cms\Content\Type\Domain\WriteModel\Model\ContentType;
use Tulia\Cms\Shared\Domain\WriteModel\Event\DomainEvent;

/**
 * @author Adam Banaszkiewicz
 */
final class ContentTypeDeleted extends DomainEvent
{
    private string $id;
    private string $type;

    public function __construct(
        string $id,
        string $type
    ) {
        $this->id = $id;
        $this->type = $type;
    }

    public static function fromModel(ContentType $contentType): self
    {
        return new self($contentType->getId(), $contentType->getType());
    }

    public function getId(): string
    {
        return $this->id;
    }

    public function getType(): string
    {
        return $this->type;
    }
}