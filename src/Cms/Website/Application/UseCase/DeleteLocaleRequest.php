<?php

declare(strict_types=1);

namespace Tulia\Cms\Website\Application\UseCase;

use Tulia\Cms\Shared\Application\UseCase\RequestInterface;

/**
 * @author Adam Banaszkiewicz
 */
final class DeleteLocaleRequest implements RequestInterface
{
    public function __construct(
        public readonly string $websiteId,
        public readonly string $code,
        public readonly CopyMachineEnum $copyMachineMode = CopyMachineEnum::RESPECT_THRESHOLD,
    ) {
    }
}
