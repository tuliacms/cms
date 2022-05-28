<?php

declare(strict_types=1);

namespace Tulia\Cms\Content\Type\Domain\AbstractModel;

use Tulia\Cms\Content\Type\Domain\WriteModel\Exception\EmptyRoutingStrategyForRoutableContentTypeException;

/**
 * @author Adam Banaszkiewicz
 */
abstract class AbstractContentType
{
    protected string $type;
    protected ?string $controller = null;
    protected string $code;
    protected string $name = '';
    protected string $icon = '';
    protected bool $isRoutable = false;
    protected bool $isHierarchical = false;
    protected ?string $routingStrategy = null;
    protected bool $isInternal = false;

    /**
     * @var AbstractField[]
     */
    protected array $fields = [];

    public function getName(): string
    {
        return $this->name;
    }

    public function setName(string $name): void
    {
        $this->name = $name;
        $this->recordChange();
    }

    public function getIcon(): string
    {
        return $this->icon;
    }

    public function setIcon(string $icon): void
    {
        $this->icon = $icon;
        $this->recordChange();
    }

    public function getCode(): string
    {
        return $this->code;
    }

    public function getType(): string
    {
        return $this->type;
    }

    /**
     * @param string|array $type
     * @return bool
     */
    public function isType($type): bool
    {
        return in_array($this->type, (array) $type, true);
    }

    public function getLayout(): AbstractLayoutType
    {
        return $this->layout;
    }

    public function getController(): ?string
    {
        return $this->controller;
    }

    public function setController(?string $controller): void
    {
        $this->controller = $controller;
        $this->recordChange();
    }

    public function isRoutable(): bool
    {
        return $this->isRoutable;
    }

    /**
     * @throws EmptyRoutingStrategyForRoutableContentTypeException
     */
    public function setIsRoutable(bool $isRoutable): void
    {
        if ($isRoutable && ! $this->routingStrategy) {
            throw EmptyRoutingStrategyForRoutableContentTypeException::fromType($this->type);
        }

        $this->isRoutable = $isRoutable;
        $this->recordChange();
    }


    public function isHierarchical(): bool
    {
        return $this->isHierarchical;
    }

    public function setIsHierarchical(bool $isHierarchical): void
    {
        $this->isHierarchical = $isHierarchical;
        $this->recordChange();
    }

    /**
     * @return AbstractField[]
     */
    public function getFields(): array
    {
        return $this->fields;
    }

    public function setFields(array $fields): void
    {
        $this->fields = $fields;
        $this->recordChange();
    }

    public function getField(string $code): AbstractField
    {
        return $this->fields[$code];
    }

    public function hasField(string $code): bool
    {
        return isset($this->fields[$code]);
    }

    public function addField(AbstractField $field): AbstractField
    {
        $this->fields[$field->getCode()] = $field;
        $this->recordChange();

        return $field;
    }

    public function getRoutingStrategy(): ?string
    {
        return $this->routingStrategy;
    }

    /**
     * @throws EmptyRoutingStrategyForRoutableContentTypeException
     */
    public function setRoutingStrategy(?string $routingStrategy): void
    {
        if ($this->isRoutable && ! $routingStrategy) {
            throw EmptyRoutingStrategyForRoutableContentTypeException::fromType($this->type);
        }

        $this->routingStrategy = $routingStrategy;
        $this->recordChange();
    }

    /**
     * @return AbstractField[]
     */
    public function flattenFields(): array
    {
        return $this->flattenFieldsRecursive($this->fields);
    }

    public function buildAttributesMapping(): array
    {
        return $this->buildAttributesMappingRecursive($this->fields);
    }

    abstract protected function recordChange(): void;

    /**
     * @param AbstractField[] $fields
     */
    private function buildAttributesMappingRecursive(array $fields, string $prefix = ''): array
    {
        $result = [];

        foreach ($fields as $field) {
            dump($field->getCode(), $field->isNonscalarValue(), $field);
            if ($field->isType('repeatable')) {
                foreach ($this->buildAttributesMappingRecursive($field->getChildren(), $prefix.$field->getCode().'.') as $code => $subfield) {
                    $result[$code] = $subfield;
                }
            } else {
                $result[$prefix.$field->getCode()] = [
                    'is_multilingual' => $field->isMultilingual(),
                    'is_compilable' => $field->is('compilable'),
                    'has_nonscalar_value' => $field->isNonscalarValue(),
                ];

                if ($field->is('compilable')) {
                    $result[$prefix.$field->getCode().':compiled'] = [
                        'is_multilingual' => $field->isMultilingual(),
                        'is_compilable' => false,
                        'has_nonscalar_value' => $field->isNonscalarValue(),
                    ];
                }
            }
        }

        return $result;
    }

    /**
     * @param AbstractField[] $fields
     * @return AbstractField[]
     */
    private function flattenFieldsRecursive(array $fields, string $prefix = ''): array
    {
        $result = [];

        foreach ($fields as $field) {
            if ($field->isType('repeatable')) {
                $flatenedSubfields = $this->flattenFieldsRecursive(
                    $field->getChildren(),
                    sprintf('%s%s.', $prefix, $field->getCode())
                );

                foreach ($flatenedSubfields as $code => $subfield) {
                    $result[$code] = $subfield;
                }
            } else {
                $result[$prefix . $field->getCode()] = $field;
            }
        }

        return $result;
    }
}
