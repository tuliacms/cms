<?php

declare(strict_types=1);

namespace Tulia\Cms\Menu\Domain\ReadModel\Finder;

use Tulia\Cms\Menu\Domain\ReadModel\Model\Menu;
use Tulia\Cms\Shared\Domain\ReadModel\Finder\Model\Collection;

/**
 * @author Adam Banaszkiewicz
 */
interface MenuFinderInterface
{
    /**
     * @param array $criteria
     * @param string $scope
     * @return null|Menu
     */
    public function findOne(array $criteria, string $scope);

    public function find(array $criteria, string $scope): Collection;
}
