<?php

declare(strict_types=1);

namespace Tulia\Cms\Website\Domain\WriteModel\Rules\CanAddLocale;

/**
 * @author Adam Banaszkiewicz
 */
enum CanAddLocaleReasonEnum: string
{
    case LocaleAlreadyExists = 'This locale already exists in this website';
    case TooManyTranslations = 'Too many translations';
    case OK = 'OK';
}
