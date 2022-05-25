<?php

declare(strict_types=1);

namespace Tulia\Cms\ContentBuilder\ContentType\Infrastructure\Framework\Form\Service;

use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\FormBuilderInterface;
use Tulia\Cms\ContentBuilder\ContentType\Domain\ReadModel\Model\ContentType;
use Tulia\Cms\ContentBuilder\ContentType\Domain\ReadModel\Model\Field;
use Tulia\Cms\ContentBuilder\ContentType\Infrastructure\Framework\Form\FormType\RepeatableGroupType;
use Tulia\Cms\ContentBuilder\Layout\Service\ConstraintsBuilder;
use Tulia\Cms\ContentBuilder\Layout\Service\FieldTypeMappingRegistry;

/**
 * @author Adam Banaszkiewicz
 */
class SymfonyFieldBuilder
{
    private FieldTypeMappingRegistry $mappingRegistry;
    private ConstraintsBuilder $constraintsBuilder;

    public function __construct(
        FieldTypeMappingRegistry $mappingRegistry,
        ConstraintsBuilder $constraintsBuilder
    ) {
        $this->mappingRegistry = $mappingRegistry;
        $this->constraintsBuilder = $constraintsBuilder;
    }

    public function buildFieldAndAddToBuilder(
        FormBuilderInterface $builder,
        ContentType $contentType,
        Field $field
    ): void {
        if ($field->getType() === 'repeatable') {
            $this->buildRepeatable($builder, $contentType, $field);
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
        Field $field
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
                ],
            ]);
    }
}
