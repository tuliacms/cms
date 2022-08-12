<?php

declare(strict_types=1);

namespace Tulia\Component\Theme\Customizer\Changeset\Storage;

use Tulia\Component\Theme\Customizer\Changeset\ChangesetInterface;
use Tulia\Component\Theme\Exception\ChangesetNotFoundException;

/**
 * @author Adam Banaszkiewicz
 */
interface StorageInterface
{
    public function has(string $id, string $locale): bool;

    /**
     * @throws ChangesetNotFoundException
     */
    public function get(string $id, string $locale): ChangesetInterface;

    public function save(ChangesetInterface $changeset, string $locale, string $defaultLocale, array $availableLocales): void;

    public function getActiveChangeset(string $theme, string $locale): ?ChangesetInterface;
}
