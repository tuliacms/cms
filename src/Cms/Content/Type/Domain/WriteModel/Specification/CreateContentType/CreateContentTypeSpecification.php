<?php

declare(strict_types=1);

namespace Tulia\Cms\Content\Type\Domain\WriteModel\Specification\CreateContentType;

use Tulia\Cms\Content\Type\Domain\ReadModel\Service\ContentTypeRegistryInterface;
use Tulia\Cms\Content\Type\Domain\WriteModel\Specification\Shared\CodeContainsValidCharactersSpecification;
use Tulia\Cms\Shared\Domain\WriteModel\Specification\ContextInterface;
use Tulia\Cms\Shared\Domain\WriteModel\Specification\SpecificationInterface;

/**
 * @author Adam Banaszkiewicz
 */
final class CreateContentTypeSpecification implements SpecificationInterface
{
    private ContentTypeRegistryInterface $contentTypeRegistry;

    public function __construct(ContentTypeRegistryInterface $contentTypeRegistry)
    {
        $this->contentTypeRegistry = $contentTypeRegistry;
    }

    public function isSatisfiedBy(ContextInterface $context): bool
    {
        return (new TypeExistsSpecification($this->contentTypeRegistry))->isSatisfiedBy($context)
            && (new CodeContainsValidCharactersSpecification())->isSatisfiedBy($context);
    }
}
