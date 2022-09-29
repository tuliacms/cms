<?php

declare(strict_types=1);

namespace Tulia\Cms\Website\Infrastructure\Persistence\LocalStorage;

use Tulia\Cms\Website\Domain\ReadModel\Finder\WebsiteFinderInterface;
use Tulia\Cms\Website\Domain\WriteModel\Query\WebsitesCounterQueryInterface;

/**
 * @author Adam Banaszkiewicz
 */
final class LocalStorageWebsitesCounterQuery implements WebsitesCounterQueryInterface
{
    public function __construct(
        private readonly WebsiteFinderInterface $finder,
    ) {
    }

    public function countActiveOnesExcept(string $except): int
    {
        $count = 0;

        foreach ($this->finder->all() as $website) {
            if ($website->getId() !== $except && $website->isActive()) {
                $count++;
            }
        }

        return $count;
    }
}
