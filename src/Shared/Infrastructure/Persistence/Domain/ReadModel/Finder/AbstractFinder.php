<?php

declare(strict_types=1);

namespace Tulia\Cms\Shared\Infrastructure\Persistence\Domain\ReadModel\Finder;

use Tulia\Cms\Shared\Domain\ReadModel\Finder\Model\Collection;
use Tulia\Cms\Shared\Infrastructure\Bus\Event\EventBusInterface;
use Tulia\Cms\Shared\Infrastructure\Persistence\Domain\ReadModel\Finder\Event\QueryFilterEvent;
use Tulia\Cms\Shared\Infrastructure\Persistence\Domain\ReadModel\Finder\Event\QueryPrepareEvent;
use Tulia\Cms\Shared\Infrastructure\Persistence\Domain\ReadModel\Finder\Plugin\PluginRegistry;
use Tulia\Cms\Shared\Infrastructure\Persistence\Domain\ReadModel\Finder\Query\QueryInterface;

/**
 * @author Adam Banaszkiewicz
 */
abstract class AbstractFinder
{
    protected EventBusInterface $eventBus;

    protected PluginRegistry $pluginRegistry;

    abstract public function getAlias(): string;

    abstract public function createQuery(): QueryInterface;

    public function setEventBus(EventBusInterface $eventBus): void
    {
        $this->eventBus = $eventBus;
    }

    public function setPluginsRegistry(PluginRegistry $pluginRegistry): void
    {
        $this->pluginRegistry = $pluginRegistry;
    }

    /**
     * @param array $criteria
     * @param string $scope
     * @return object|null
     */
    public function findOne(array $criteria, string $scope)
    {
        $criteria['limit'] = 1;
        $criteria = $this->transformStringableObjectToPrimitives($criteria);

        return $this->find($criteria, $scope)->first();
    }

    public function find(array $criteria, string $scope): Collection
    {
        $criteria = $this->transformStringableObjectToPrimitives($criteria);
        [$criteria, $scope] = $this->prepareFetch($criteria, $scope);
        $query = $this->createQuery();
        $query->setPluginsRegistry($this->pluginRegistry);

        $result = $query->query($criteria, $scope);

        return $this->afterQuery($result, $criteria, $scope);
    }

    protected function prepareFetch(array $criteria, string $scope): array
    {
        $event = new QueryPrepareEvent($criteria, $scope, []);
        $this->eventBus->dispatch($event);

        return [$event->getCriteria(), $event->getScope()];
    }

    protected function transformStringableObjectToPrimitives(array $criteria): array
    {
        foreach ($criteria as $key => $val) {
            // Transforms \Tulia\Cms\Attributes\Domain\ReadModel\Model\AttributeValue to string,
            // to prevents errors when casting to integers in pagination query.
            if (is_object($val) && $val instanceof \Stringable) {
                $criteria[$key] = (string) $val;
            }
        }

        return $criteria;
    }

    protected function afterQuery(Collection $collection, array $criteria, string $scope): Collection
    {
        $event = new QueryFilterEvent($collection, $criteria, $scope, $this->getAlias(), []);
        $this->eventBus->dispatch($event);

        return $event->getCollection();
    }
}
