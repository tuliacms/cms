<?php

declare(strict_types=1);

namespace Tulia\Cms\Website\Domain\WriteModel\Rules\CannAddLocale;

/**
 * @author Adam Banaszkiewicz
 */
enum CanAddLocaleReasonEnum: string
{
    case LocaleAlreadyExists = 'This locale already exists in this website';
    case OK = 'OK';
}
