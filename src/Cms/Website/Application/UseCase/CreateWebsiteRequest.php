<?php

declare(strict_types=1);

namespace Tulia\Cms\Website\Application\UseCase;

use Tulia\Cms\Shared\Application\UseCase\RequestInterface;

/**
 * @author Adam Banaszkiewicz
 */
final class CreateWebsiteRequest implements RequestInterface
{
    public function __construct(
        public readonly string $name,
        public readonly bool $active,
        public readonly string $locale,
        public readonly string $domain,
        public readonly string $backendPrefix,
        public readonly string $domainDevelopment,
        public readonly ?string $pathPrefix,
        public readonly string $sslMode,
    ) {
    }
}
