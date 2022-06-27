<?php

declare(strict_types=1);

namespace Tulia\Cms\Content\Type\Domain\WriteModel\Event;

use Tulia\Cms\Shared\Domain\WriteModel\Event\AbstractDomainEvent;

/**
 * @author Adam Banaszkiewicz
 */
final class FieldsGroupAdded extends AbstractDomainEvent
{
    private string $contentType;
    private string $code;
    private string $section;
    private string $name;

    public function __construct(
        string $contentType,
        string $code,
        string $section,
        string $name
    ) {
        $this->contentType = $contentType;
        $this->code = $code;
        $this->section = $section;
        $this->name = $name;
    }

    public function getContentType(): string
    {
        return $this->contentType;
    }

    public function getCode(): string
    {
        return $this->code;
    }

    public function getSection(): string
    {
        return $this->section;
    }

    public function getName(): string
    {
        return $this->name;
    }
}
