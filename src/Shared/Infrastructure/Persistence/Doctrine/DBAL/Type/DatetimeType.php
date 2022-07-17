<?php

declare(strict_types=1);

namespace Tulia\Cms\Shared\Infrastructure\Persistence\Doctrine\DBAL\Type;

use Carbon\Doctrine\CarbonImmutableType;
use Tulia\Cms\Shared\Domain\WriteModel\Model\ValueObject\DateTime;

/**
 * @author Adam Banaszkiewicz
 */
final class DatetimeType extends CarbonImmutableType
{
    protected function getCarbonClassName(): string
    {
        return DateTime::class;
    }

    public function getName(): string
    {
        return 'datetime';
    }
}
