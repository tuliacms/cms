<?php

declare(strict_types=1);

namespace Tulia\Cms\Content\Type\Domain\WriteModel\Specification\Shared;

use Tulia\Cms\Content\Type\Domain\WriteModel\Specification\CreateContentType\CreateContentTypeContext;
use Tulia\Cms\Shared\Domain\WriteModel\Specification\SpecificationInterface;

/**
 * @author Adam Banaszkiewicz
 */
final class CodeContainsValidCharactersSpecification implements SpecificationInterface
{
    /**
     * @param CreateContentTypeContext $context
     */
    public function isSatisfiedBy(object $context): bool
    {
        return (bool) preg_match('#^[a-z0-9_]+$#', $context->getCode());
    }
}
