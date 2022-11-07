<?php

declare(strict_types=1);

namespace Tulia\Cms\Platform\Shared\Document;

/**
 * @author Adam Banaszkiewicz
 */
interface DocumentInterface
{
    public function setTitle(?string $title): void;
    public function getTitle(): ?string;
    public function setDescription(?string $description): void;
    public function getDescription(): ?string;
    public function setKeywords(?string $keywords): void;
    public function getKeywords(): ?string;
    public function setAttribute(?string $name, ?string $value): void;
    public function getAttribute(?string $name, ?string $default = null);
    public function getAttributes(): array;
    public function setAttributes(array $attributes): void;
    public function addLink(array $attributes): void;
    public function getLinks(): array;
    public function setLinks(array $links): void;
    public function buildLinks(): string;
    public function addMeta(string $name, string $value): void;
    public function getMetas(): array;
    public function setMetas(array $metas): void;
    public function buildMetas(): string;
}
