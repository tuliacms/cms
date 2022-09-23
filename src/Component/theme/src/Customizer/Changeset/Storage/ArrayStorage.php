<?php

declare(strict_types=1);

namespace Tulia\Component\Theme\Customizer\Changeset\Storage;

use Tulia\Component\Theme\Customizer\Changeset\ChangesetInterface;

/**
 * @author Adam Banaszkiewicz
 */
class ArrayStorage implements StorageInterface
{
    protected $changesets = [];

    public function has(string $id, string $websiteId, string $locale): bool
    {
        return isset($this->changesets[$websiteId][$id]);
    }

    public function get(string $id, string $websiteId, string $locale): ChangesetInterface
    {
        return $this->changesets[$websiteId][$id];
    }

    public function getActiveChangeset(string $theme, string $websiteId, string $locale): ?ChangesetInterface
    {
        return null;
    }
}
