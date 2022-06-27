<?php

declare(strict_types=1);

namespace Tulia\Cms\Content\Type\Domain\WriteModel\Event;

use Tulia\Cms\Shared\Domain\WriteModel\Event\AbstractDomainEvent;

/**
 * @author Adam Banaszkiewicz
 */
final class FieldsGroupRemoved extends AbstractDomainEvent
{
    private string $contentType;
    private string $groupCode;

    public function __construct(
        string $contentType,
        string $groupCode
    ) {
        $this->contentType = $contentType;
        $this->groupCode = $groupCode;
    }

    public function getContentType(): string
    {
        return $this->contentType;
    }

    public function getGroupCode(): string
    {
        return $this->groupCode;
    }
}
