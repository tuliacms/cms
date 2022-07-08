<?php

declare(strict_types = 1);

namespace Tulia\Cms\Widget\Domain\Catalog\Storage;

/**
 * @author Adam Banaszkiewicz
 */
interface StorageInterface
{
    public function all(?string $space, string $locale): array;

    public function findById(string $id, string $locale): ?array;

    public function findBySpace(string $space, string $locale): array;
}
