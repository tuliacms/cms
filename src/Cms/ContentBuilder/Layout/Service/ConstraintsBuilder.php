<?php

declare(strict_types=1);

namespace Tulia\Cms\ContentBuilder\Layout\Service;

use Symfony\Component\Validator\Constraint;
use Tulia\Cms\ContentBuilder\Layout\Exception\ConstraintNotExistsException;

/**
 * @author Adam Banaszkiewicz
 */
class ConstraintsBuilder
{
    private ConstraintTypeMappingRegistry $mapping;

    public function __construct(ConstraintTypeMappingRegistry $mapping)
    {
        $this->mapping = $mapping;
    }

    /**
     * @throws ConstraintNotExistsException
     */
    public function build(array $constraints): array
    {
        if ($constraints === []) {
            return [];
        }

        $result = [];

        foreach ($constraints as $code => $constraint) {
            if ($constraint instanceof Constraint) {
                $result[] = $constraint;
            } else {
                $result[] = $this->mapping->getConstraint($code, [$constraint['modificators'] ?? []]);
            }
        }

        return $result;
    }
}
