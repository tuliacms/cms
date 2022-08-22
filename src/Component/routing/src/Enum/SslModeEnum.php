<?php

declare(strict_types=1);

namespace Tulia\Component\Routing\Enum;

/**
 * @author Adam Banaszkiewicz
 */
enum SslModeEnum: string
{
    case ALLOWED_BOTH  = 'ALLOWED_BOTH';
    case FORCE_SSL     = 'FORCE_SSL';
    case FORCE_NON_SSL = 'FORCE_NON_SSL';
}
