<?php

declare(strict_types=1);

namespace Tulia\Cms\Content\Type\Infrastructure\Framework\Form\Service;

use Tulia\Cms\Content\Attributes\Domain\WriteModel\Model\Attribute;
use Tulia\Cms\Content\Attributes\Domain\WriteModel\Service\UriToArrayTransformer;
use Tulia\Cms\Content\Type\Domain\ReadModel\Service\ContentTypeRegistryInterface;
use Tulia\Cms\Content\Type\Domain\ReadModel\Service\FieldTypeMappingRegistry;
use Tulia\Cms\Content\Type\Infrastructure\Framework\Form\ContentTypeFormDescriptor;
use Tulia\Component\Routing\Website\WebsiteInterface;

/**
 * @author Adam Banaszkiewicz
 */
class ContentFormService
{
    public function __construct(
        private readonly ContentTypeRegistryInterface $contentTypeRegistry,
        private readonly SymfonyFormBuilderCreator $formBuilder,
        private readonly UriToArrayTransformer $attributesToArrayTransformer,
        private readonly FieldTypeMappingRegistry $fieldTypeMappingRegistry,
    ) {
    }

    /**
     * @param Attribute[] $attributes
     */
    public function buildFormDescriptor(
        WebsiteInterface $website,
        string $typeCode,
        array $attributes,
        array $viewContext = []
    ): ContentTypeFormDescriptor {
        $contentType = $this->contentTypeRegistry->get($typeCode);
        $flattened = $this->attributesToArrayTransformer->transform($attributes);
        $form = $this->formBuilder->createBuilder($website, $contentType, $flattened);

        return new ContentTypeFormDescriptor($this->fieldTypeMappingRegistry, $contentType, $form, $viewContext);
    }
}
