<?php

declare(strict_types=1);

namespace Tulia\Cms\Content\Type\Domain\WriteModel\Event;

use Tulia\Cms\Shared\Domain\WriteModel\Event\DomainEvent;

/**
 * @author Adam Banaszkiewicz
 */
final class FieldUpdated extends DomainEvent
{
    private string $contentType;
    private string $code;

    public function __construct(
        string $contentType,
        string $code
    ) {
        $this->contentType = $contentType;
        $this->code = $code;
    }

    public function getContentType(): string
    {
        return $this->contentType;
    }

    public function getCode(): string
    {
        return $this->code;
    }
}
