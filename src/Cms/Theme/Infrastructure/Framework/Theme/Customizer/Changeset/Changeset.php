<?php

declare(strict_types=1);

namespace Tulia\Cms\Theme\Infrastructure\Framework\Theme\Customizer\Changeset;

use Tulia\Component\Theme\Customizer\Changeset\Changeset as BaseChangeset;

/**
 * @author Adam Banaszkiewicz
 */
class Changeset extends BaseChangeset implements ChangesetInterface
{
    private ?string $authorId;

    public function getAuthorId(): ?string
    {
        return $this->authorId;
    }

    public function setAuthorId(?string $authorId): void
    {
        $this->authorId = $authorId;
    }
}
