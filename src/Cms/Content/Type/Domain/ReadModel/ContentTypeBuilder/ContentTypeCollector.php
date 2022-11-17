<?php

declare(strict_types=1);

namespace Tulia\Cms\Content\Type\Domain\ReadModel\ContentTypeBuilder;

use Tulia\Cms\Content\Type\Domain\ReadModel\ContentTypeBuilder\Definition\ContentTypeDefinition;

/**
 * @author Adam Banaszkiewicz
 */
final class ContentTypeCollector
{
    private array $types = [];

    public function newOne(string $type, string $code, string $name): ContentTypeDefinition
    {
        return $this->types[$code] = new ContentTypeDefinition($type, $code, $name);
    }

    /**
     * @return ContentTypeDefinition[]
     */
    public function all(): array
    {
        return $this->types;
    }
}
