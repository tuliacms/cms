<?php

declare(strict_types=1);

namespace Tulia\Cms\Content\Type\Domain\ReadModel\Service;

/**
 * @author Adam Banaszkiewicz
 */
final class ConstraintsResolver implements ConstraintsResolverInterface
{
    private ConstraintTypeMappingRegistry $constraintTypeMappingRegistry;

    public function __construct(ConstraintTypeMappingRegistry $constraintTypeMappingRegistry)
    {
        $this->constraintTypeMappingRegistry = $constraintTypeMappingRegistry;
    }

    public function resolve(array $field): array
    {
        $constraints = [];

        foreach ($field['constraints'] as $constraint) {
            $constraints[$constraint] = $this->constraintTypeMappingRegistry->get($constraint);
        }

        if (isset($field['custom_constraints'])) {
            $field['constraints'] = array_merge(
                $constraints,
                $field['custom_constraints']
            );
        } else {
            $field['constraints'] = $constraints;
        }

        // Remove custom constraints as those were merged with named constraints
        unset($field['custom_constraints']);

        return $field;
    }
}
