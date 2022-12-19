<?php

declare(strict_types=1);

namespace Tulia\Cms\Content\Type\Domain\ReadModel\FieldChoicesProvider;

/**
 * @author Adam Banaszkiewicz
 */
interface FieldChoicesProviderInterface
{
    public function provide(): array;
}
