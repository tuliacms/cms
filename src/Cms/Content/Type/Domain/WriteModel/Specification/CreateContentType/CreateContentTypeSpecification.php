<?php

declare(strict_types=1);

namespace Tulia\Cms\Content\Type\Domain\WriteModel\Specification\CreateContentType;

use Tulia\Cms\Content\Type\Domain\ReadModel\Service\ContentTypeRegistryInterface;
use Tulia\Cms\Content\Type\Domain\WriteModel\Service\ContentTypeExistanceDetectorInterface;
use Tulia\Cms\Content\Type\Domain\WriteModel\Specification\Shared\CodeContainsValidCharactersSpecification;
use Tulia\Cms\Shared\Domain\WriteModel\Specification\AndSpecification;
use Tulia\Cms\Shared\Domain\WriteModel\Specification\NotSpecification;
use Tulia\Cms\Shared\Domain\WriteModel\Specification\SpecificationInterface;

/**
 * @author Adam Banaszkiewicz
 */
final class CreateContentTypeSpecification implements SpecificationInterface
{
    public function __construct(
        private ContentTypeRegistryInterface $contentTypeRegistry,
        private ContentTypeExistanceDetectorInterface $detector
    ) {
    }

    public function isSatisfiedBy(object $context): bool
    {
        $spec = new AndSpecification(
            new TypeExistsSpecification($this->contentTypeRegistry),
            new CodeContainsValidCharactersSpecification(),
            new NotSpecification(
                new ContentTypeWithCodeExistsSpecification($this->detector)
            )
        );

        return $spec->isSatisfiedBy($context);
    }
}
