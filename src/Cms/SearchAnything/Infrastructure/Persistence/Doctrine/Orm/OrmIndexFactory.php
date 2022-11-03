<?php

declare(strict_types=1);

namespace Tulia\Cms\SearchAnything\Infrastructure\Persistence\Doctrine\Orm;

use Doctrine\DBAL\Connection;
use Tulia\Cms\SearchAnything\Domain\WriteModel\Enum\LocalizationStrategyEnum;
use Tulia\Cms\SearchAnything\Domain\WriteModel\Enum\MultisiteStrategyEnum;
use Tulia\Cms\SearchAnything\Domain\WriteModel\Service\DocumentCollectorRegistryInterface;
use Tulia\Cms\SearchAnything\Domain\WriteModel\Service\IndexFactoryInterface;
use Tulia\Cms\SearchAnything\Domain\WriteModel\Service\IndexInterface;

/**
 * @author Adam Banaszkiewicz
 */
final class OrmIndexFactory implements IndexFactoryInterface
{
    /** @var array OrmIndex */
    private array $cache = [];

    public function __construct(
        private readonly DocumentCollectorRegistryInterface $collectorRegistry,
        private readonly DocumentRepository $repository,
        private readonly Connection $connection,
    ) {
    }

    public function create(
        string $name,
        LocalizationStrategyEnum $localizationStrategy,
        MultisiteStrategyEnum $multisiteStrategy,
        string $collector,
    ): IndexInterface {
        if (isset($this->cache[$name])) {
            return $this->cache[$name];
        }

        return $this->cache[$name] = new OrmIndex(
            $this->repository,
            $this->connection,
            $name,
            $localizationStrategy->value,
            $multisiteStrategy->value,
            $this->collectorRegistry->get($collector),
        );
    }
}
