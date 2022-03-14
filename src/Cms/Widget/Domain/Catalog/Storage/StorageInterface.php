<?php

declare(strict_types = 1);

namespace Tulia\Cms\Widget\Domain\Catalog\Storage;

/**
 * @author Adam Banaszkiewicz
 */
interface StorageInterface
{
    public function all(?string $space): array;

    public function findById(string $id): ?array;

    public function findBySpace(string $space): array;
}
