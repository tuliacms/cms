<?php

declare(strict_types=1);

namespace Tulia\Cms\Shared\Domain\WriteModel;

/**
 * @author Adam Banaszkiewicz
 */
interface UuidGeneratorInterface
{
    public function generate(): string;
}
