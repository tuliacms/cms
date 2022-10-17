<?php

declare(strict_types=1);

namespace Tulia\Cms\Content\Type\Infrastructure\Framework\Form\Service;

use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\FormBuilderInterface;
use Tulia\Cms\Content\Type\Domain\ReadModel\Model\ContentType;
use Tulia\Cms\Content\Type\Domain\ReadModel\Model\Field;
use Tulia\Cms\Content\Type\Domain\ReadModel\Service\ConstraintsBuilderInterface;
use Tulia\Cms\Content\Type\Domain\ReadModel\Service\FieldTypeMappingRegistry;
use Tulia\Cms\Content\Type\Infrastructure\Framework\Form\FormType\RepeatableGroupType;
use Tulia\Cms\Platform\Infrastructure\Framework\Routing\Website\WebsiteInterface;

/**
 * @author Adam Banaszkiewicz
 */
class SymfonyFieldBuilder
{
    public function __construct(
        private readonly FieldTypeMappingRegistry $mappingRegistry,
        private readonly ConstraintsBuilderInterface $constraintsBuilder,
    ) {
    }

    public function buildFieldAndAddToBuilder(
        FormBuilderInterface $builder,
        ContentType $contentType,
        Field $field,
        WebsiteInterface $website,
    ): void {
        if ($field->getType() === 'repeatable') {
            $this->buildRepeatable($builder, $contentType, $field, $website);
            return;
        }

        $typeBuilder = $this->mappingRegistry->getTypeBuilder($field->getType());
        $typeHandler = $this->mappingRegistry->getTypeHandler($field->getType());

        $options = [
            'label' => $field->getName() === ''
                ? false
                : $field->getName(),
            'translation_domain' => $field->getTranslationDomain(),
            'constraints' => $this->constraintsBuilder->build($field->getConstraints()),
            'content_builder_field' => $field,
            'content_builder_field_handler' => $typeHandler,
            'locale' => $website->getLocale()->getCode(),
            'website_id' => $website->getId(),
        ];

        if ($typeBuilder) {
            $options = $typeBuilder->buildOptions($field, $options, $contentType);
        }

        $builder->add(
            $field->getCode(),
            $this->mappingRegistry->getTypeClassname($field->getType()),
            $options
        );

        if ($typeHandler) {
            $builder->get($field->getCode())->addModelTransformer(new FieldTypeHandlerAwareDataTransformer($field, $typeHandler));
        }
    }

    private function buildRepeatable(
        FormBuilderInterface $builder,
        ContentType $contentType,
        Field $field,
        WebsiteInterface $website,
    ): void {
        $prototypeName = sprintf('__name_%s__', $field->getCode());

        $builder->add(
            $field->getCode(),
            CollectionType::class,
            [
                'label' => $field->getName(),
                'allow_add' => true,
                'allow_delete' => true,
                'attr' => [
                    'class' => 'repeatable-field',
                    'data-prototype-name' => $prototypeName,
                    'data-dynamic-element' => 'repeatable-element',
                ],
                'prototype_name' => $prototypeName,
                'entry_type' => RepeatableGroupType::class,
                'entry_options' => [
                    'label' => false,
                    'fields' => $field->getChildren(),
                    'repeatable_field' => true,
                    'content_type' => $contentType,
                    'website' => $website,
                ],
            ]);
    }
}
