<?php

declare(strict_types=1);

namespace Tulia\Cms\Content\Type\UserInterface\Web\Backend\Form;

use Symfony\Component\Form\FormInterface;
use Tulia\Cms\Content\Attributes\Domain\WriteModel\Model\Attribute;
use Tulia\Cms\Content\Type\Domain\ReadModel\FieldTypeBuilder\FieldTypeBuilderInterface;
use Tulia\Cms\Content\Type\Domain\ReadModel\FieldTypeHandler\FieldTypeHandlerInterface;
use Tulia\Cms\Content\Type\Domain\ReadModel\Model\Field;
use Tulia\Cms\Content\Type\Domain\ReadModel\Service\ContentTypeRegistryInterface;
use Tulia\Cms\Content\Type\Domain\ReadModel\Service\FieldTypeMappingRegistry;

/**
 * @author Adam Banaszkiewicz
 */
final class FormAttributesExtractor
{
    public function __construct(
        private readonly ContentTypeRegistryInterface $contentTypeRegistry,
        private readonly FieldTypeMappingRegistry $fieldTypeMappingRegistry,
    ) {
    }

    public function extractData(FormInterface $form, string $contentType): array
    {
        $data = $form->getData();
        $fields = iterator_to_array($form);

        /** @var FormInterface $field */
        foreach ($fields as $name => $field) {
            if ($field->getConfig()->hasOption('__this_is_content_type_attributes_form')) {
                $data[$name] = $this->extractFromField($field, $contentType);
            }
        }

        return $data;
    }

    public function extractFromField(FormInterface $form, string $contentType): array
    {
        // @todo Mayby we can compile here those attributes? Like in \Tulia\Cms\Node\Application\UseCase\AbstractNodeUseCase::processAttributes
        return $this->flattenFields(
            $this->contentTypeRegistry->get($contentType)->getFields(),
            $this->handleFields(iterator_to_array($form), $form->getData())
        );
    }

    /**
     * @param Field[] $fields
     */
    private function flattenFields(array $fields, array $rawData, string $uniquePrefix = '', string $prefix = ''): array
    {
        $result = [];

        foreach ($fields as $field) {
            if ($field->isType('repeatable')) {
                $subfieldsGroups = array_values($rawData[$field->getCode()]);
                usort($subfieldsGroups, function (array $a, array $b) {
                    return $a['__order'] <=> $b['__order'];
                });

                foreach ($subfieldsGroups as $groupKey => $subfields) {
                    if ($uniquePrefix) {
                        $fieldCode = '[' . $field->getCode() . ']';
                    } else {
                        $fieldCode = $field->getCode();
                    }

                    $flatenedSubfields = $this->flattenFields(
                        $field->getChildren(),
                        $subfields,
                        sprintf('%s%s[%d]', $uniquePrefix, $fieldCode, $groupKey),
                        sprintf('%s%s.', $prefix, $field->getCode())
                    );

                    foreach ($flatenedSubfields as $code => $subfield) {
                        $result[$code] = $subfield;
                    }
                }
            } else {
                if ($uniquePrefix) {
                    $uri = $uniquePrefix . '[' . $field->getCode() . ']';
                } else {
                    $uri = $uniquePrefix . $field->getCode();
                }

                $flags = $field->getFlags();

                if ($field->isMultilingual()) {
                    $flags[] = 'multilingual';
                }

                $result[$uri] = new Attribute(
                    $prefix . $field->getCode(),
                    $uri,
                    [],
                    null,
                    [],
                    $flags,
                );

                $builder = $this->fieldTypeMappingRegistry->getTypeBuilder($field->getType());

                if ($builder instanceof FieldTypeBuilderInterface) {
                    $result[$uri] = $builder->buildAttributeFromValue($field, $result[$uri], $rawData[$field->getCode()]);
                } else {
                    $result[$uri] = $result[$uri]->withValue($rawData[$field->getCode()]);
                }
            }
        }

        return $result;
    }

    /**
     * @param FormInterface[] $fields
     */
    private function handleFields(array $fields, array $rawData): array
    {
        foreach ($fields as $code => $field) {
            $fieldType = $field->getConfig()->getOption('content_builder_field');
            $handler = $field->getConfig()->getOption('content_builder_field_handler');

            if ($handler instanceof FieldTypeHandlerInterface) {
                $rawData[$code] = $handler->handle($fieldType, $rawData[$code] ?? null);
            }

            $children = iterator_to_array($field);

            if ($children !== []) {
                $this->handleFields($children, $rawData[$code] ?? []);
            }
        }

        return $rawData;
    }
}
