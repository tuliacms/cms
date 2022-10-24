<?php

declare(strict_types=1);

namespace Tulia\Cms\Website\Domain\WriteModel\Rules\CanDeleteLocale;

/**
 * @author Adam Banaszkiewicz
 */
enum CanDeleteLocaleReasonEnum: string
{
    case CannotDeleteLocaleThatYouAreOn = 'Cannot delete locale that You are on';
    case LocaleDoesNotExists = 'Locale does not exists';
    case CannotDeleteDefaultLocale = 'Cannot delete default locale';
    case OK = 'OK';
}
