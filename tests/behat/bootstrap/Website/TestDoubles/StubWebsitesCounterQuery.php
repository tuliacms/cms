<?php

declare(strict_types=1);

namespace Tulia\Cms\Tests\Behat\Website\TestDoubles;

use Tulia\Cms\Website\Domain\WriteModel\Query\WebsitesCounterQueryInterface;

/**
 * @author Adam Banaszkiewicz
 */
final class StubWebsitesCounterQuery implements WebsitesCounterQueryInterface
{
    private int $returnValue = 0;

    public function makeReturnValue(int $value): void
    {
        $this->returnValue = $value;
    }

    public function countActiveOnesExcept(string $except): int
    {
        return $this->returnValue;
    }
}
