<?php

declare(strict_types=1);

namespace Tulia\Cms\SearchAnything\Domain\WriteModel\Enum;

/**
 * @author Adam Banaszkiewicz
 */
enum MultisiteStrategyEnum: string
{
    case GLOBAL = 'global';
    case WEBSITE = 'website';
}
