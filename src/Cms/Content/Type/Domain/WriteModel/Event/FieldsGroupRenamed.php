<?php

declare(strict_types=1);

namespace Tulia\Cms\Content\Type\Domain\WriteModel\Event;

use Tulia\Cms\Shared\Domain\WriteModel\Event\AbstractDomainEvent;

/**
 * @author Adam Banaszkiewicz
 */
final class FieldsGroupRenamed extends AbstractDomainEvent
{
    private string $contentType;
    private string $groupCode;
    private string $name;

    public function __construct(
        string $contentType,
        string $groupCode,
        string $name
    ) {
        $this->contentType = $contentType;
        $this->groupCode = $groupCode;
        $this->name = $name;
    }

    public function getContentType(): string
    {
        return $this->contentType;
    }

    public function getGroupCode(): string
    {
        return $this->groupCode;
    }

    public function getName(): string
    {
        return $this->name;
    }
}
