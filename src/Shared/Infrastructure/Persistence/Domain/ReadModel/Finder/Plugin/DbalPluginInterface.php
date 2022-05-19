<?php

declare(strict_types=1);

namespace Tulia\Cms\Shared\Infrastructure\Persistence\Domain\ReadModel\Finder\Plugin;

use Tulia\Cms\Shared\Infrastructure\Persistence\Domain\ReadModel\Finder\Query\AbstractQuery;

/**
 * @author Adam Banaszkiewicz
 */
interface DbalPluginInterface extends PluginInterface
{
    public function handle(AbstractQuery $query, array $criteria): void;
}
