<?php

declare(strict_types=1);

namespace Tulia\Cms\Website\Infrastructure\Persistence\Doctrine\Dbal;

use Tulia\Cms\Website\Domain\WriteModel\Query\WebsitesCounterQueryInterface;

/**
 * @author Adam Banaszkiewicz
 */
final class DbalWebsitesCounterQuery implements WebsitesCounterQueryInterface
{
    public function __construct(
        private readonly CachedDbalWebsitesStorage $storage,
    ) {
    }

    public function countActiveOnesExcept(string $except): int
    {
        $count = 0;

        foreach ($this->storage->all() as $website) {
            if ($website['id'] !== $except && $website['active']) {
                $count++;
            }
        }

        return $count;
    }
}
