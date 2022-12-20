<?php

declare(strict_types=1);

namespace Tulia\Cms\Widget\Application\UseCase;

use Tulia\Cms\Shared\Application\UseCase\RequestInterface;

/**
 * @author Adam Banaszkiewicz
 */
final class UpdateWidgetRequest implements RequestInterface
{
    public function __construct(
        public readonly string $id,
        public readonly array $data,
        public readonly string $locale,
        public readonly string $defaultLocale,
        public readonly array $localeCodes,
    ) {
    }
}
