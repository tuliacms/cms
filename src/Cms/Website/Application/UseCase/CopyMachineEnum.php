<?php

declare(strict_types=1);

namespace Tulia\Cms\Website\Application\UseCase;

/**
 * @author Adam Banaszkiewicz
 */
enum CopyMachineEnum
{
    case DISABLE_PROCESSING;
    case RESPECT_THRESHOLD;
    case PROCESS_ALL;
}
