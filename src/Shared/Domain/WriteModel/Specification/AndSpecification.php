<?php

declare(strict_types=1);

namespace Tulia\Cms\Shared\Domain\WriteModel\Specification;

/**
 * @author Adam Banaszkiewicz
 */
final class AndSpecification implements SpecificationInterface
{
    private array $specifications;

    public function __construct(
        SpecificationInterface ...$specifications
    ) {
        $this->specifications = $specifications;
    }

    public function isSatisfiedBy(object $context): bool
    {
        foreach ($this->specifications as $specification) {
            if ($specification->isSatisfiedBy($context) === false) {
                return false;
            }
        }

        return true;
    }
}
