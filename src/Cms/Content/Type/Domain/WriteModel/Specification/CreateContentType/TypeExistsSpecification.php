<?php

declare(strict_types=1);

namespace Tulia\Cms\Content\Type\Domain\WriteModel\Specification\CreateContentType;

use Tulia\Cms\Content\Type\Domain\ReadModel\Service\ContentTypeRegistryInterface;
use Tulia\Cms\Shared\Domain\WriteModel\Specification\SpecificationInterface;

/**
 * @author Adam Banaszkiewicz
 */
final class TypeExistsSpecification implements SpecificationInterface
{
    private ContentTypeRegistryInterface $contentTypeRegistry;

    public function __construct(ContentTypeRegistryInterface $contentTypeRegistry)
    {
        $this->contentTypeRegistry = $contentTypeRegistry;
    }

    /**
     * @param CreateContentTypeContext $context
     */
    public function isSatisfiedBy(object $context): bool
    {
        return $this->contentTypeRegistry->has($context->getType());
    }
}
