<?php

declare(strict_types=1);

namespace Tulia\Cms\Content\Type\Domain\WriteModel\Specification\CreateContentType;

/**
 * @author Adam Banaszkiewicz
 */
final class CreateContentTypeContext
{
    private string $code;
    private string $type;

    public function __construct(string $code, string $type)
    {
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
