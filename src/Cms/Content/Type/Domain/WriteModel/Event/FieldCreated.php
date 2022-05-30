<?php

declare(strict_types=1);

namespace Tulia\Cms\Content\Type\Domain\WriteModel\Event;

use Tulia\Cms\Shared\Domain\WriteModel\Event\DomainEvent;

/**
 * @author Adam Banaszkiewicz
 */
final class FieldCreated extends DomainEvent
{
    private string $contentType;
    private string $code;
    private string $name;
    private string $type;

    public function __construct(
        string $contentType,
        string $code,
        string $name,
        string $type
    ) {
        $this->contentType = $contentType;
        $this->code = $code;
        $this->name = $name;
        $this->type = $type;
    }

    public function getContentType(): string
    {
        return $this->contentType;
    }

    public function getCode(): string
    {
        return $this->code;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getType(): string
    {
        return $this->type;
    }
}