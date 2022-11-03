<?php

declare(strict_types=1);

namespace Tulia\Cms\SearchAnything\Domain\WriteModel\Service;

use Tulia\Cms\SearchAnything\Domain\WriteModel\Enum\LocalizationStrategyEnum;
use Tulia\Cms\SearchAnything\Domain\WriteModel\Enum\MultisiteStrategyEnum;

/**
 * @author Adam Banaszkiewicz
 */
interface IndexFactoryInterface
{
    public function create(
        string $name,
        bool $multilingual,
        LocalizationStrategyEnum $localizationStrategy,
        MultisiteStrategyEnum $multisiteStrategy,
        string $collector,
    ): IndexInterface;
}
