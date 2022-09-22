<?php

declare(strict_types=1);

namespace Tulia\Cms\Theme\Domain\WriteModel\Exception;

use Tulia\Cms\Shared\Domain\WriteModel\Exception\AbstractDomainException;

/**
 * @author Adam Banaszkiewicz
 */
final class ChangesetDoesNotExists extends AbstractDomainException
{
    public static function fromId(string $id, string $theme): self
    {
        return new self(sprintf('Changeset %s for theme %s does not exists', $id, $theme));
    }
}
