<?php

declare(strict_types=1);

namespace Tulia\Cms\Content\Type\Domain\ReadModel\Service;

/**
 * @author Adam Banaszkiewicz
 */
interface ConstraintsBuilderInterface
{
    public function build(array $constraints): array;
}
