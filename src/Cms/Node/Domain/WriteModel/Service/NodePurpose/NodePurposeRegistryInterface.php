<?php

declare(strict_types=1);

namespace Tulia\Cms\Node\Domain\WriteModel\Service\NodePurpose;

use Tulia\Cms\Node\Domain\WriteModel\Exception\NodePurposeNotFoundException;

/**
 * @author Adam Banaszkiewicz
 */
interface NodePurposeRegistryInterface
{
    public function all(): array;

    /**
     * @throws NodePurposeNotFoundException
     */
    public function isSingular(string $name): bool;

    public function has(string $name): bool;
    public function get(string $name): array;
    public function add(string $name, bool $singular): void;
}
