<?php

declare(strict_types=1);

namespace Tulia\Cms\Shared\Infrastructure\Persistence\Domain\ReadModel\Finder\Event;

use Symfony\Contracts\EventDispatcher\Event;
use Tulia\Cms\Shared\Domain\ReadModel\Finder\Model\Collection;

/**
 * @author Adam Banaszkiewicz
 */
class QueryFilterEvent extends Event
{
    private Collection $collection;

    protected array $criteria = [];

    protected string $scope;

    protected string $alias;

    private array $parameters;

    public function __construct(Collection $collection, array $criteria, string $scope, string $alias, array $parameters)
    {
        $this->collection = $collection;
        $this->criteria = $criteria;
        $this->scope = $scope;
        $this->alias = $alias;
        $this->parameters = $parameters;
    }

    public function getCollection(): Collection
    {
        return $this->collection;
    }

    public function setCollection(Collection $collection): void
    {
        $this->collection = $collection;
    }

    public function getCriteria(): array
    {
        return $this->criteria;
    }

    public function getParameters(): array
    {
        return $this->parameters;
    }

    public function getAlias(): string
    {
        return $this->alias;
    }

    public function getScope(): string
    {
        return $this->scope;
    }

    public function hasScope(array $scopes): bool
    {
        return \in_array($this->scope, $scopes, true);
    }
}
