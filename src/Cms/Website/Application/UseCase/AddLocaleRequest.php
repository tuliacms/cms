<?php

declare(strict_types=1);

namespace Tulia\Cms\Website\Application\UseCase;

use Tulia\Cms\Shared\Application\UseCase\RequestInterface;

/**
 * @author Adam Banaszkiewicz
 */
final class AddLocaleRequest implements RequestInterface
{
    public function __construct(
        public readonly string $websiteId,
        public readonly string $code,
        public readonly ?string $domain,
        public readonly ?string $domainDevelopment,
        public readonly ?string $localePrefix,
        public readonly ?string $pathPrefix,
        public readonly string $sslMode,
        public readonly CopyMachineEnum $copyMachineMode = CopyMachineEnum::RESPECT_THRESHOLD,
    ) {
    }
}
