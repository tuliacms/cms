<?php

declare(strict_types=1);

namespace Tulia\Cms\Content\Type\Domain\WriteModel\Specification\CreateContentType;

use Tulia\Cms\Content\Type\Domain\WriteModel\Service\ContentTypeExistanceDetectorInterface;
use Tulia\Cms\Shared\Domain\WriteModel\Specification\SpecificationInterface;

/**
 * @author Adam Banaszkiewicz
 */
final class ContentTypeWithCodeExistsSpecification implements SpecificationInterface
{
    public function __construct(
        private ContentTypeExistanceDetectorInterface $detector
    ) {
    }

    /**
     * @param CreateContentTypeContext $context
     */
    public function isSatisfiedBy(object $context): bool
    {
        return $this->detector->exists($context->getCode());
    }
}
