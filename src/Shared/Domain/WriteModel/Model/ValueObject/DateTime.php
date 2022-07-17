<?php

declare(strict_types=1);

namespace Tulia\Cms\Shared\Domain\WriteModel\Model\ValueObject;

use Carbon\Carbon;

/**
 * @author Adam Banaszkiewicz
 */
final class DateTime extends Carbon
{
    public function toStringWithPrecision(): string
    {
        return $this->format('Y-m-d H:i:s.u');
    }
}
