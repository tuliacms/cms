<?php

declare(strict_types=1);

namespace Tulia\Cms\Theme\Domain\WriteModel\Model;

/**
 * @author Adam Banaszkiewicz
 * @final
 */
class ChangesetTranslation
{
    private string $id;

    public function __construct(
        private readonly ?Changeset $changeset,
        private readonly string $locale,
        private array $payload,
    ) {
    }

    public function isFor(string $locale): bool
    {
        return $this->locale === $locale;
    }

    public function copy(Changeset $newChangeset): self
    {
        $copy = new self($newChangeset, $this->locale, $this->payload);
        $copy->payload = $this->payload;

        return $copy;
    }

    public function updatePayload(array $payload): void
    {
        $this->payload = $payload;
    }
}
