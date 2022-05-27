<?php

declare(strict_types=1);

namespace Tulia\Cms\Content\Type\Infrastructure\Framework\Form;

use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\HttpFoundation\Request;
use Tulia\Cms\Content\Attributes\Domain\WriteModel\Model\Attribute;
use Tulia\Cms\Content\Type\Domain\ReadModel\FieldTypeBuilder\FieldTypeBuilderInterface;
use Tulia\Cms\Content\Type\Domain\ReadModel\FieldTypeHandler\FieldTypeHandlerInterface;
use Tulia\Cms\Content\Type\Domain\ReadModel\Model\ContentType;
use Tulia\Cms\Content\Type\Domain\ReadModel\Model\Field;
use Tulia\Cms\Content\Type\Domain\ReadModel\Service\FieldTypeMappingRegistry;

/**
 * @author Adam Banaszkiewicz
 */
class ContentTypeFormDescriptor
{
    private FieldTypeMappingRegistry $fieldTypeMappingRegistry;
    private ContentType $contentType;
    private FormBuilderInterface $formBuilder;
    private ?FormInterface $form = null;
    private ?FormView $formView = null;
    private bool $formClosed = false;
    private array $data = [];
    private array $viewContext;

    public function __construct(
        FieldTypeMappingRegistry $fieldTypeMappingRegistry,
        ContentType $contentType,
        FormBuilderInterface $formBuilder,
        array $viewContext = []
    ) {
        $this->fieldTypeMappingRegistry = $fieldTypeMappingRegistry;
        $this->formBuilder = $formBuilder;
        $this->contentType = $contentType;
        $this->viewContext = $viewContext;
    }

    /**
     * @return Field[]
     */
    protected function getFields(): array
    {
        return $this->contentType->getFields();
    }

    public function getFormBuilder(): FormBuilderInterface
    {
        if ($this->formClosed) {
            throw new \RuntimeException('Form is already closed. Cannot modify form after doing any operations on it.');
        }

        return $this->formBuilder;
    }

    public function getForm(): FormInterface
    {
        if ($this->formClosed) {
            return $this->form;
        }

        $this->formClosed = true;
        return $this->form = $this->formBuilder->getForm();
    }

    public function getFormView(): FormView
    {
        if ($this->formView) {
            return $this->formView;
        }

        return $this->formView = $this->getForm()->createView();
    }

    /**
     * @return Attribute[]
     */
    public function getData(): array
    {
        if ($this->data !== []) {
            return $this->data;
        }

        $rawData = $this->handleFields(iterator_to_array($this->getForm()), $this->getForm()->getData());

        $this->data = $this->flattenFields($this->getFields(), $rawData);

        return $this->data;
    }

    public function isFormValid(): bool
    {
        return $this->getForm()->isSubmitted() && $this->getForm()->isValid();
    }

    public function getContentType(): ContentType
    {
        return $this->contentType;
    }

    public function handleRequest(Request $request): void
    {
        $this->getForm()->handleRequest($request);
    }

    public function getViewContext(): array
    {
        return $this->viewContext;
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

                $result[$uri] = new Attribute(
                    $prefix . $field->getCode(),
                    $uri,
                    $rawData[$field->getCode()],
                    null,
                    [],
                    $field->getFlags() + [
                        'multilingual' => $field->isMultilingual(),
                        'non_scalar_value' => $field->hasNonscalarValue(),
                    ]
                );

                $builder = $this->fieldTypeMappingRegistry->getTypeBuilder($field->getType());

                if ($builder instanceof FieldTypeBuilderInterface) {
                    $result[$uri] = $builder->buildAttributeFromValue($field, $result[$uri], $rawData[$field->getCode()]);
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
