<?php

declare(strict_types=1);

namespace Tulia\Cms\Menu\Domain\ReadModel\Query;

/**
 * @author Adam Banaszkiewicz
 */
interface MenuExportCollectorInterface
{
    public function collect(string $websiteId): array;
}
