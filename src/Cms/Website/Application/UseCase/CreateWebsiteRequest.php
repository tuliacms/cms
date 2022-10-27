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
        public readonly bool $enabled,
        public readonly string $locale,
        public readonly string $domain,
        public readonly string $domainDevelopment,
        public readonly ?string $pathPrefix = null,
        public readonly ?string $sslMode = null,
        public readonly ?string $backendPrefix = null,
    ) {
    }
}
