<?php

declare(strict_types=1);

namespace Tulia\Cms\Content\Type\Domain\WriteModel\Rules;

/**
 * @author Adam Banaszkiewicz
 */
interface CanCreateContentTypeInterface
{
    public function decide(string $type, string $code): CanCreateContentTypeReason;
}
