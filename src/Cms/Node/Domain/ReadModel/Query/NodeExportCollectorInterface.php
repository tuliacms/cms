<?php

declare(strict_types=1);

namespace Tulia\Cms\Node\Domain\ReadModel\Query;

/**
 * @author Adam Banaszkiewicz
 */
interface NodeExportCollectorInterface
{
    public function collect(string $websiteId, string $locale): array;
}
