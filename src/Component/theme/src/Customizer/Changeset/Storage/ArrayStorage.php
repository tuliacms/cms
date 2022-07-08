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

    public function has(string $id, string $locale): bool
    {
        return isset($this->changesets[$id]);
    }

    public function get(string $id, string $locale): ChangesetInterface
    {
        return $this->changesets[$id];
    }

    public function remove(ChangesetInterface $changeset)
    {
       unset($this->changesets[$changeset->getId()]);
    }

    public function save(ChangesetInterface $changeset, string $locale): void
    {
        $this->changesets[$changeset->getId()] = $changeset;
    }

    public function getActiveChangeset(string $theme, string $locale): ?ChangesetInterface
    {
        return null;
    }
}
