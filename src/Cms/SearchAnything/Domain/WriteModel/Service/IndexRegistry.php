<?php

declare(strict_types=1);

namespace Tulia\Cms\SearchAnything\Domain\WriteModel\Service;

use Tulia\Cms\SearchAnything\Domain\WriteModel\Enum\LocalizationStrategyEnum;
use Tulia\Cms\SearchAnything\Domain\WriteModel\Enum\MultisiteStrategyEnum;

/**
 * @author Adam Banaszkiewicz
 */
class IndexRegistry
{
    /** @var IndexInterface[] */
    private array $cache = [];

    public function __construct(
        private readonly IndexFactoryInterface $indexFactory,
        private readonly array $indexes,
    ) {
    }

    public function get(string $name): IndexInterface
    {
        if (isset($this->cache[$name])) {
            return $this->cache[$name];
        }

        $index = $this->indexes[$name];

        return $this->cache[$name] = $this->indexFactory->create(
            $name,
            LocalizationStrategyEnum::tryFrom($index['localization_strategy']),
            MultisiteStrategyEnum::tryFrom($index['multisite_strategy']),
            $index['collector'],
        );
    }

    public function has(string $name): bool
    {
        return isset($this->indexes[$name]);
    }

    /**
     * @return IndexInterface[]
     */
    public function all(): array
    {
        $indexes = [];

        foreach ($this->indexes as $name => $index) {
            $indexes[] = $this->get($name);
        }

        return $indexes;
    }
}
