<?php

declare(strict_types=1);

namespace Tulia\Cms\User\Infrastructure\Framework\Validator\Constraints;

use Symfony\Component\Validator\Constraint;

/**
 * @Annotation
 *
 * @author Adam Banaszkiewicz
 */
#[\Attribute(\Attribute::TARGET_PROPERTY | \Attribute::TARGET_METHOD | \Attribute::IS_REPEATABLE)]
class Password extends Constraint
{
}
