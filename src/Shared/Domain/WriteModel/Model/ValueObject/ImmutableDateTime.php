<?php

declare(strict_types=1);

namespace Tulia\Cms\Shared\Domain\WriteModel\Model\ValueObject;

use Carbon\CarbonImmutable;

/**
 * @author Adam Banaszkiewicz
 */
final class ImmutableDateTime extends CarbonImmutable
{
    public function toStringWithPrecision(): string
    {
        return $this->format('Y-m-d H:i:s.u');
    }
}
