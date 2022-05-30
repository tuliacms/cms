<?php

declare(strict_types=1);

namespace Tulia\Cms\Content\Type\Domain\WriteModel\Event;

use Tulia\Cms\Shared\Domain\WriteModel\Event\DomainEvent;

/**
 * @author Adam Banaszkiewicz
 */
final class ContentTypeDeleted extends DomainEvent
{
    private string $code;

    public function __construct(
        string $code
    ) {
        $this->code = $code;
    }

    public function getCode(): string
    {
        return $this->code;
    }
}
