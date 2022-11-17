<?php

declare(strict_types=1);

namespace Tulia\Cms\Content\Type\Domain\ReadModel\ContentTypeBuilder\Definition;

/**
 * @author Adam Banaszkiewicz
 */
final class FieldDefinition
{
    public function __construct(
        public readonly string $code,
        public readonly string $type,
        public string $name,
        public string $translationDomain = 'content_builder.field',
        public array $flags = [],
        public array $configuration = [],
        public array $constraints = [],
        public ?string $parent = null,
        public int $position = 0,
    ) {
    }

    public static function fromArray(array $data): self
    {
        return new self(
            code: $data['code'],
            type: $data['type'],
            name: $data['name'],
            translationDomain: $data['translation_domain'] ?? 'content_builder.field',
            flags: $data['flags'] ?? [],
            configuration: $data['configuration'] ?? [],
            constraints: $data['constraints'] ?? [],
            parent: $data['parent'] ?? null,
            position: $data['position'] ?? 0,
        );
    }
}
