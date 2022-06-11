<?php

declare(strict_types=1);

namespace Tulia\Cms\Tests\Behat\Content\Type\TestDoubles;

use Tulia\Cms\Content\Type\Domain\WriteModel\Rules\CanCreateContentTypeInterface;
use Tulia\Cms\Content\Type\Domain\WriteModel\Rules\CanCreateContentTypeReason;

/**
 * @author Adam Banaszkiewicz
 */
final class AlwaysTrueCanCreateContentTypeStub implements CanCreateContentTypeInterface
{
    public function decide(string $type, string $code): CanCreateContentTypeReason
    {
        return CanCreateContentTypeReason::OK;
    }
}
