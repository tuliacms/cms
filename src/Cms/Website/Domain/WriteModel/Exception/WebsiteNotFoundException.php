<?php

declare(strict_types=1);

namespace Tulia\Cms\Website\Domain\WriteModel\Exception;

use Tulia\Cms\Shared\Domain\WriteModel\Exception\AbstractDomainException;

/**
 * @author Adam Banaszkiewicz
 */
class WebsiteNotFoundException extends AbstractDomainException
{
    public static function fromId(string $id): self
    {
        return new self(sprintf('Website %s does not exists', $id));
    }
}
