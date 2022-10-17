<?php

declare(strict_types=1);

namespace Tulia\Cms\Website\Domain\ReadModel\Finder;

use Tulia\Cms\Website\Domain\ReadModel\Model\Website;

/**
 * @author Adam Banaszkiewicz
 */
interface WebsiteFinderInterface
{
    /** @return Website[] */
    public function all(): array;

    public function get(string $id): Website;
}
