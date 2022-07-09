<?php

declare(strict_types=1);

namespace Tulia\Cms\Node\Domain\WriteModel\Service;

/**
 * @author Adam Banaszkiewicz
 */
interface NodeSlugUniquenessInterface
{
    public function isUnique(string $locale, string $slug, ?string $notInThisNode = null): bool;
}
