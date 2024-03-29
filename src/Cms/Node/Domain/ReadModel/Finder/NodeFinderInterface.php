<?php

declare(strict_types=1);

namespace Tulia\Cms\Node\Domain\ReadModel\Finder;

use Tulia\Cms\Shared\Domain\ReadModel\Finder\Model\Collection;
use Tulia\Cms\Node\Domain\ReadModel\Model\Node;

/**
 * @author Adam Banaszkiewicz
 */
interface NodeFinderInterface
{
    /**
     * @param array $criteria
     * @param string $scope
     * @return null|Node
     */
    public function findOne(array $criteria, string $scope);

    /**
     * @return Node[]|Collection
     */
    public function find(array $criteria, string $scope): Collection;
}
