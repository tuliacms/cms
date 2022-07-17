<?php

declare(strict_types=1);

namespace Tulia\Cms\Shared\Infrastructure\Persistence\Doctrine\DBAL\Type;

use Carbon\Doctrine\CarbonImmutableType;
use Tulia\Cms\Shared\Domain\WriteModel\Model\ValueObject\ImmutableDateTime;

/**
 * @author Adam Banaszkiewicz
 */
final class ImmutableDatetimeType extends CarbonImmutableType
{
    protected function getCarbonClassName(): string
    {
        return ImmutableDateTime::class;
    }

    public function getName(): string
    {
        return 'datetime_immutable';
    }
}
