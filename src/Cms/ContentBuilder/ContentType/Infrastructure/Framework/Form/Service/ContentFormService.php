<?php

declare(strict_types=1);

namespace Tulia\Cms\ContentBuilder\ContentType\Infrastructure\Framework\Form\Service;

use Tulia\Cms\ContentBuilder\Attributes\Domain\WriteModel\Model\Attribute;
use Tulia\Cms\ContentBuilder\Attributes\Domain\WriteModel\Service\UriToArrayTransformer;
use Tulia\Cms\ContentBuilder\ContentType\Domain\ReadModel\Service\ContentTypeRegistryInterface;
use Tulia\Cms\ContentBuilder\ContentType\Infrastructure\Framework\Form\ContentTypeFormDescriptor;
use Tulia\Cms\ContentBuilder\Layout\Service\FieldTypeMappingRegistry;

/**
 * @author Adam Banaszkiewicz
 */
class ContentFormService
{
    private ContentTypeRegistryInterface $contentTypeRegistry;
    private SymfonyFormBuilderCreator $formBuilder;
    private UriToArrayTransformer $attributesToArrayTransformer;
    private FieldTypeMappingRegistry $fieldTypeMappingRegistry;

    public function __construct(
        ContentTypeRegistryInterface $contentTypeRegistry,
        SymfonyFormBuilderCreator $formBuilder,
        UriToArrayTransformer $attributesToArrayTransformer,
        FieldTypeMappingRegistry $fieldTypeMappingRegistry
    ) {
        $this->contentTypeRegistry = $contentTypeRegistry;
        $this->formBuilder = $formBuilder;
        $this->attributesToArrayTransformer = $attributesToArrayTransformer;
        $this->fieldTypeMappingRegistry = $fieldTypeMappingRegistry;
    }

    /**
     * @param Attribute[] $attributes
     */
    public function buildFormDescriptor(string $type, array $attributes, array $viewContext = []): ContentTypeFormDescriptor
    {
        $contentType = $this->contentTypeRegistry->get($type);
        $flattened = $this->attributesToArrayTransformer->transform($attributes);
        $form = $this->formBuilder->createBuilder($contentType, $flattened);

        return new ContentTypeFormDescriptor($this->fieldTypeMappingRegistry, $contentType, $form, $viewContext);
    }
}
