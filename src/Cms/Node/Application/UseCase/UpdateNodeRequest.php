<?php

declare(strict_types=1);

namespace Tulia\Cms\Node\Application\UseCase;

use Tulia\Cms\Shared\Application\UseCase\RequestInterface;

/**
 * @author Adam Banaszkiewicz
 */
final class UpdateNodeRequest implements RequestInterface
{
    public function __construct(
        public readonly string $id,
        public readonly array $data,
        public readonly string $defaultLocale,
        public readonly string $locale,
    ) {
    }

    public function isDefaultLocale(): bool
    {
        return $this->locale === $this->defaultLocale;
    }
}
