<?php

declare(strict_types=1);

namespace Tulia\Cms\Options\Infrastructure\Framework\Config;

use Tulia\Cms\Options\Domain\WriteModel\Service\RegisteredOptionsRegistryInterface;

/**
 * @author Adam Banaszkiewicz
 */
final class YamlRegisteredOptionsRegistry implements RegisteredOptionsRegistryInterface
{
    public function __construct(
        private array $definitions
    ) {
    }

    public function all(): array
    {
        return $this->definitions;
    }
}
