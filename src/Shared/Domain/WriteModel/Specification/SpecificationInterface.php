<?php

declare(strict_types=1);

namespace Tulia\Cms\Shared\Domain\WriteModel\Specification;

/**
 * @author Adam Banaszkiewicz
 */
interface SpecificationInterface
{
    public function isSatisfiedBy(ContextInterface $context): bool;
}
