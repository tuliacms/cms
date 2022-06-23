<?php

declare(strict_types=1);

namespace Tulia\Cms\Content\Type\Domain\ReadModel\Service;

use Symfony\Component\Validator\Constraint;

/**
 * @author Adam Banaszkiewicz
 */
class ConstraintsBuilder implements ConstraintsBuilderInterface
{
    private ConstraintTypeMappingRegistry $mapping;

    public function __construct(ConstraintTypeMappingRegistry $mapping)
    {
        $this->mapping = $mapping;
    }

    public function build(array $constraints): array
    {
        if ($constraints === []) {
            return [];
        }

        $result = [];

        foreach ($constraints as $constraint) {
            if ($constraint instanceof Constraint) {
                $result[] = $constraint;
            } else {
                $result[] = $this->mapping->getConstraint($constraint['code'], [$constraint['modificators'] ?? []]);
            }
        }

        return $result;
    }
}
