<?php

declare(strict_types=1);

namespace Tulia\Cms\Content\Attributes\Domain\ReadModel\Service;

/**
 * @author Adam Banaszkiewicz
 */
interface AttributesFinderInterface
{
    public function find(string $ownerId, string $locale): array;
}
