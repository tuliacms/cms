<?php

declare(strict_types=1);

namespace Tulia\Cms\Node\Domain\ReadModel\Query;

/**
 * @author Adam Banaszkiewicz
 */
interface NodeSearchCollectorInterface
{
    public function collectTranslationsOfNode(string $id): array;
    public function collectDocumentsOfLocale(string $locale, int $offset, int $limit): array;
    public function countDocumentsOfLocale(string $locale): int;
}
