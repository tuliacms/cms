<?php

declare(strict_types=1);

namespace Tulia\Component\Importer\ObjectImporter\Traits;

/**
 * @author Adam Banaszkiewicz
 */
trait AuthorAwareTrait
{
    private string $authorId;

    public function getAuthorId(): string
    {
        return $this->authorId;
    }

    public function setAuthorId(string $authorId): void
    {
        $this->authorId = $authorId;
    }
}
