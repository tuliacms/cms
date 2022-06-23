<?php

declare(strict_types=1);

namespace Tulia\Cms\Content\Attributes\Domain\WriteModel;

/**
 * @author Adam Banaszkiewicz
 */
interface AttributesRepositoryInterface
{
    public function findAllAggregated(string $type, array $ownerIdList, array $info): array;

    public function findAll(string $type, string $ownerId, array $info): array;

    public function persist(string $type, string $ownerId, array $attributes): void;

    public function delete(string $type, string $ownerId): void;
}
