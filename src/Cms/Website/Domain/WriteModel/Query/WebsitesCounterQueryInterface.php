<?php

declare(strict_types=1);

namespace Tulia\Cms\Website\Domain\WriteModel\Query;

/**
 * @author Adam Banaszkiewicz
 */
interface WebsitesCounterQueryInterface
{
    public function countActiveOnesExcept(string $except): int;
}
