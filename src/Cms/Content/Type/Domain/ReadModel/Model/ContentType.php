<?php

declare(strict_types=1);

namespace Tulia\Cms\Content\Type\Domain\ReadModel\Model;

use Tulia\Cms\Content\Type\Domain\WriteModel\Exception\EmptyRoutingStrategyForRoutableContentTypeException;

/**
 * @author Adam Banaszkiewicz
 */
final class ContentType
{
    protected ?string $icon = null;
    protected bool $isRoutable = false;
    protected bool $isHierarchical = false;
    protected ?string $routingStrategy = null;
    protected ?string $controller = null;
    protected bool $isInternal = false;

    /**
     * @var FieldsGroup[]
     */
    protected array $fieldGroups = [];

    public function __construct(
        protected string $type,
        protected string $code,
        protected string $name,
    ) {
    }

    public static function fromArray(array $data): self
    {
        $fieldGroups = [];

        foreach ($data['fields_groups'] as $group) {
            $fieldGroups[] = FieldsGroup::fromArray($group);
        }

        $self = new self($data['type'], $data['code'], $data['name']);
        $self->icon = $data['icon'];
        $self->isRoutable = (bool) $data['is_routable'];
        $self->isHierarchical = (bool) $data['is_hierarchical'];
        $self->routingStrategy = $data['routing_strategy'];
        $self->controller = $data['controller'];
        $self->fieldGroups = $fieldGroups;

        return $self;
    }

    public function getCode(): string
    {
        return $this->code;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getIcon(): ?string
    {
        return $this->icon;
    }

    /*public function addField(Field $field): void
    {
        //throw new \Exception('Please implements this method.');
    }*/

    public function getField(string $code): Field
    {
        return $this->getFields()[$code];
    }

    public function hasField(string $code): bool
    {
        return \array_key_exists($code, $this->getFields());
    }

    /**
     * @return Field[]
     */
    public function getFields(): array
    {
        $source = [[]];

        foreach ($this->fieldGroups as $group) {
            $source[] = $group->getFields();
        }

        return array_merge(...$source);
    }

    /**
     * @return FieldsGroup[]
     */
    public function getFieldGroups(): array
    {
        return $this->fieldGroups;
    }

    public function isRoutable(): bool
    {
        return $this->isRoutable;
    }

    public function isHierarchical(): bool
    {
        return $this->isHierarchical;
    }

    public function isInternal(): bool
    {
        return $this->isInternal;
    }

    /**
     * @param string|array $type
     * @return bool
     */
    public function isType($type): bool
    {
        return in_array($this->type, (array) $type, true);
    }

    public function getType(): string
    {
        return $this->type;
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
    }

    public function getRoutingStrategy(): ?string
    {
        return $this->routingStrategy;
    }

    /**
     * @return Field[]
     */
    public function flattenFields(): array
    {
        return $this->flattenFieldsRecursive($this->getFields());
    }

    public function buildAttributesMapping(): array
    {
        return $this->buildAttributesMappingRecursive($this->getFields());
    }

    public function getController(): string
    {
        return $this->controller;
    }

    /**
     * @param Field[] $fields
     */
    private function buildAttributesMappingRecursive(array $fields, string $prefix = ''): array
    {
        $result = [];

        foreach ($fields as $field) {
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
     * @param Field[] $fields
     * @return Field[]
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
