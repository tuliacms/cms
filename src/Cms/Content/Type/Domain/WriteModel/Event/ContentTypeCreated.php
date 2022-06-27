<?php

declare(strict_types=1);

namespace Tulia\Cms\Content\Type\Domain\WriteModel\Event;

use Tulia\Cms\Shared\Domain\WriteModel\Event\AbstractDomainEvent;

/**
 * @author Adam Banaszkiewicz
 */
final class ContentTypeCreated extends AbstractDomainEvent
{
    private string $code;
    private string $type;

    public function __construct(
        string $code,
        string $type
    ) {
        $this->code = $code;
        $this->type = $type;
    }

    public function getCode(): string
    {
        return $this->code;
    }

    public function getType(): string
    {
        return $this->type;
    }
}
