<?php

declare(strict_types=1);

namespace Tulia\Cms\Platform\Application\UseCase;

use Tulia\Cms\Shared\Application\UseCase\RequestInterface;

/**
 * @author Adam Banaszkiewicz
 */
final class SetupSystemRequest implements RequestInterface
{
    public function __construct(
        public readonly string $websiteName,
        public readonly string $websiteLocale,
        public readonly string $websiteLocalDomain,
        public readonly string $websiteProductionDomain,
        public readonly string $username,
        public readonly string $userPassword,
        public readonly bool $installSampleData,
    ) {
    }
}
