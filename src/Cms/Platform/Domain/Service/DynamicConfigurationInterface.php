<?php

declare(strict_types=1);

namespace Tulia\Cms\Platform\Domain\Service;

/**
 * @author Adam Banaszkiewicz
 */
interface DynamicConfigurationInterface
{
    public function open(): void;
    public function write(): void;
    public function set(string $key, null|string|int|float|array $value): void;
    public function get(string $key, mixed $default = null): mixed;
    public function remove(string $key): void;
}
