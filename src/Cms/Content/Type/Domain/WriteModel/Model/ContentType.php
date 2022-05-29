<?php

declare(strict_types=1);

namespace Tulia\Cms\Content\Type\Domain\WriteModel\Model;

use Tulia\Cms\Content\Type\Domain\WriteModel\Event\ContentTypeCreated;
use Tulia\Cms\Content\Type\Domain\WriteModel\Exception\ParentFieldNotExistsException;
use Tulia\Cms\Shared\Domain\WriteModel\Model\AggregateRootTrait;

/**
 * @author Adam Banaszkiewicz
 */
final class ContentType
{
    use AggregateRootTrait;

    private string $code;
    private string $type;
    private string $name;
    private ?string $routingStrategy = null;
    private ?string $icon = null;
    private bool $isRoutable = false;
    private bool $isHierarchical = false;
    private array $fieldGroups = [];

    /**
     * @throws ParentFieldNotExistsException
     */
    private function __construct(
        string $code,
        string $type,
        string $name,
        string $icon,
        ?string $routingStrategy,
        bool $isHierarchical,
        array $fieldGroups = []
    ) {
        $this->code = $code;
        $this->type = $type;
        $this->name = $name;
        $this->icon = $icon;
        $this->routingStrategy = $routingStrategy;
        $this->isHierarchical = $isHierarchical;

        if ($routingStrategy) {
            $this->isRoutable = true;
        }

        foreach ($fieldGroups as $group) {
            $fieldsGroup = new FieldsGroup($group['code'], $group['section'], $group['name']);
            $this->fieldGroups[] = $fieldsGroup;

            foreach ($group['fields'] as $field) {
                $fieldsGroup->addField(new Field(
                    $field['code'],
                    $field['type'],
                    $field['name'],
                    $field['flags'],
                    $field['constraints'],
                    $field['configuration'],
                    $field['parent'],
                ));
            }
        }
    }

    public static function create(
        string $code,
        string $type,
        string $name,
        string $icon,
        ?string $routingStrategy,
        bool $isHierarchical,
        array $fieldGroups = []
    ): self {
        $self = new self($code, $type, $name, $icon, $routingStrategy, $isHierarchical, $fieldGroups);
        $self->recordThat(new ContentTypeCreated($code, $type));

        return $self;
    }

    public static function fromArray(array $data): self
    {
        return new self(
            $data['code'],
            $data['type'],
            $data['name'],
            $data['icon'],
            $data['routing_strategy'],
            (bool) $data['is_hierarchical'],
            $data['fields_groups']
        );
    }

    public function toArray(): array
    {
        return [
            'code' => $this->code,
            'type' => $this->type,
            'name' => $this->name,
            'routing_strategy' => $this->routingStrategy,
            'icon' => $this->icon,
            'is_routable' => $this->isRoutable,
            'is_hierarchical' => $this->isHierarchical,
            'fields_groups' => array_map(
                fn (FieldsGroup $group) => $group->toArray(),
                $this->fieldGroups
            ),
        ];
    }

    public function rename(string $name): void
    {
        $this->name = $name;
    }

    public function assignIcon(string $icon): void
    {
        $this->icon = $icon;
    }

    public function enableRoutable(string $routingStrategy): void
    {
        $this->isRoutable = true;
        $this->routingStrategy = $routingStrategy;
    }

    public function disableRoutable(): void
    {
        $this->isRoutable = false;
        $this->routingStrategy = null;
    }

    public function enableHierarchical(): void
    {
        $this->isHierachical = true;
    }

    public function disableHierarchical(): void
    {
        $this->isHierachical = false;
    }
}
