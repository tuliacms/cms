<?php

declare(strict_types=1);

namespace Tulia\Cms\Content\Type\Domain\WriteModel\Specification\Shared;

use Tulia\Cms\Shared\Domain\WriteModel\Specification\ContextInterface;

/**
 * @author Adam Banaszkiewicz
 */
final class CodeContainsValidCharactersSpecification
{
    public function isSatisfiedBy(ContextInterface $context): bool
    {
        return (bool) preg_match('#^[a-z0-9_]+$#', $context->getCode());
    }
}
