<?php

declare(strict_types=1);

namespace Tulia\Cms\SearchAnything\Domain\WriteModel\Enum;

/**
 * @author Adam Banaszkiewicz
 */
enum LocalizationStrategyEnum: string
{
    case USER = 'user';
    case CONTENT = 'content';
    case UNILINGUAL = 'unilingual';
}
