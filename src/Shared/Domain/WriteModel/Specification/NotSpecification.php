<?php

declare(strict_types=1);

namespace Tulia\Cms\Shared\Domain\WriteModel\Specification;

/**
 * @author Adam Banaszkiewicz
 */
final class NotSpecification implements SpecificationInterface
{
    public function __construct(
        private SpecificationInterface $specification
    ) {
    }

    public function isSatisfiedBy(object $context): bool
    {
        return !$this->specification->isSatisfiedBy($context);
    }
}
