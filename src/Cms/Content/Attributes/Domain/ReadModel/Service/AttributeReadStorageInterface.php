<?php

declare(strict_types=1);

namespace Tulia\Cms\Content\Attributes\Domain\ReadModel\Service;

/**
 * @author Adam Banaszkiewicz
 */
interface AttributeReadStorageInterface
{
    public function findAll(string $type, array $ownerId, string $locale): array;
}
