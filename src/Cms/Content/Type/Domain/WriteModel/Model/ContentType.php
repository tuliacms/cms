<?php

declare(strict_types=1);

namespace Tulia\Cms\Content\Type\Domain\WriteModel\Model;

use Tulia\Cms\Content\Type\Domain\WriteModel\Event\ContentTypeCreated;
use Tulia\Cms\Content\Type\Domain\WriteModel\Event\ContentTypeUpdated;
use Tulia\Cms\Content\Type\Domain\WriteModel\Event\FieldAdded;
use Tulia\Cms\Content\Type\Domain\WriteModel\Event\FieldRemoved;
use Tulia\Cms\Content\Type\Domain\WriteModel\Event\FieldsGroupAdded;
use Tulia\Cms\Content\Type\Domain\WriteModel\Event\FieldsGroupRemoved;
use Tulia\Cms\Content\Type\Domain\WriteModel\Event\FieldsGroupRenamed;
use Tulia\Cms\Content\Type\Domain\WriteModel\Event\FieldsGroupsSorted;
use Tulia\Cms\Content\Type\Domain\WriteModel\Event\FieldsSorted;
use Tulia\Cms\Content\Type\Domain\WriteModel\Event\FieldUpdated;
use Tulia\Cms\Content\Type\Domain\WriteModel\Exception\ContentTypeCannotBeCreatedException;
use Tulia\Cms\Content\Type\Domain\WriteModel\Exception\FieldWithThatCodeAlreadyExistsException;
use Tulia\Cms\Content\Type\Domain\WriteModel\Exception\GroupWithCodeExistsException;
use Tulia\Cms\Content\Type\Domain\WriteModel\Exception\ParentFieldNotExistsException;
use Tulia\Cms\Content\Type\Domain\WriteModel\Rules\CanCreateContentTypeInterface;
use Tulia\Cms\Content\Type\Domain\WriteModel\Rules\CanCreateContentTypeReason;
use Tulia\Cms\Shared\Domain\WriteModel\Model\AbstractAggregateRoot;

/**
 * @author Adam Banaszkiewicz
 */
final class ContentType extends AbstractAggregateRoot
{
    private string $code;
    private string $type;
    private string $name;
    private ?string $routingStrategy = null;
    private ?string $icon = null;
    private bool $isRoutable = false;
    private bool $isHierarchical = false;
    /** @var FieldsGroup[] */
    private array $fieldGroups = [];
    private bool $updateRecorded = false;

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
            $fieldsGroup = new FieldsGroup($group['code'], $group['section'], $group['name'], $group['position'], $group['active']);
            $this->fieldGroups[$group['code']] = $fieldsGroup;

            $position = 1;
            foreach ($group['fields'] as $field) {
                $fieldsGroup->addField(new Field(
                    $field['code'],
                    $field['type'],
                    $field['name'],
                    $field['flags'],
                    $field['constraints'],
                    $field['configuration'],
                    $field['parent'],
                    $position++
                ));
            }
        }
    }

    public static function create(
        CanCreateContentTypeInterface $rules,
        string $code,
        string $type,
        string $name,
        string $icon = 'fas fa-boxes',
        ?string $routingStrategy = null,
        bool $isHierarchical = false,
        array $fieldGroups = []
    ): ?self {
        $reason = $rules->decide($type, $code);

        if ($reason !== CanCreateContentTypeReason::OK) {
            throw ContentTypeCannotBeCreatedException::fromReason($reason);
        }

        $self = new self($code, $type, $name, $icon, $routingStrategy, $isHierarchical, $fieldGroups);
        $self->recordThat(new ContentTypeCreated($self->code, $self->type));

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

    public function getCode(): string
    {
        return $this->code;
    }

    public function rename(string $name): void
    {
        if ($name !== $this->name) {
            $this->name = $name;
            $this->recordUpdate();
        }
    }

    public function assignIcon(string $icon): void
    {
        if ($icon !== $this->icon) {
            $this->icon = $icon;
            $this->recordUpdate();
        }
    }

    public function enableRoutable(string $routingStrategy): void
    {
        if ($this->isRoutable === false || $routingStrategy !== $this->routingStrategy) {
            $this->isRoutable = true;
            $this->routingStrategy = $routingStrategy;
            $this->recordUpdate();
        }
    }

    public function disableRoutable(): void
    {
        if ($this->isRoutable === true) {
            $this->isRoutable = false;
            $this->routingStrategy = null;
            $this->recordUpdate();
        }
    }

    public function enableHierarchical(): void
    {
        if ($this->isHierarchical === false) {
            $this->isHierarchical = true;
            $this->recordUpdate();
        }
    }

    public function disableHierarchical(): void
    {
        if ($this->isHierarchical === true) {
            $this->isHierarchical = false;
            $this->recordUpdate();
        }
    }

    public function addFieldsGroup(string $code, string $name, string $section, int $position = 0, bool $active = false): void
    {
        foreach ($this->fieldGroups as $group) {
            if ($group->getCode() === $code) {
                throw GroupWithCodeExistsException::fromCode($code);
            }
        }

        $this->fieldGroups[$code] = new FieldsGroup($code, $section, $name, $position, $active);
        $this->recordThat(new FieldsGroupAdded($this->code, $code, $section, $name));
        $this->recordUpdate();
    }

    public function removeFieldsGroup(string $code): void
    {
        foreach ($this->fieldGroups as $key => $group) {
            if ($group->getCode() === $code) {
                unset($this->fieldGroups[$key]);
                $this->recordThat(new FieldsGroupRemoved($this->code, $code));
                $this->recordUpdate();
            }
        }
    }

    public function renameFieldsGroup(string $groupCode, string $name): void
    {
        foreach ($this->fieldGroups as $fieldsGroup) {
            if ($fieldsGroup->getCode() === $groupCode) {
                if ($fieldsGroup->rename($name)) {
                    $this->recordThat(new FieldsGroupRenamed($this->code, $groupCode, $fieldsGroup->getName()));
                    $this->recordUpdate();
                }
            }
        }
    }

    public function changeFieldsGroupActivity(string $groupCode, bool $active): void
    {
        foreach ($this->fieldGroups as $fieldsGroup) {
            if ($fieldsGroup->getCode() === $groupCode) {
                $fieldsGroup->changeActivity($active);
                //$this->recordThat(new FieldsGroupRenamed($this->code, $groupCode, $fieldsGroup->getName()));
                $this->recordUpdate();
            }
        }
    }

    /**
     * @throws ParentFieldNotExistsException
     * @throws FieldWithThatCodeAlreadyExistsException
     */
    public function addFieldToGroup(
        string $groupCode,
        string $code,
        string $type,
        string $name,
        array $flags = [],
        array $constraints = [],
        array $configuration = [],
        ?string $parent = null,
        int $position = 0
    ): void {
        foreach ($this->fieldGroups as $fieldsGroup) {
            if ($fieldsGroup->getCode() === $groupCode) {
                $field = new Field($code, $type, $name, $flags, $constraints, $configuration, $parent, $position);
                $fieldsGroup->addField($field);

                $this->recordThat(new FieldAdded($this->code, $field->getCode(), $field->getName(), $field->getType(), $field->getParent()));
                $this->recordUpdate();
            }
        }
    }

    public function updateField(
        string $code,
        string $name,
        array $flags = [],
        array $constraints = [],
        array $configuration = [],
        ?string $parent = null
    ): void {
        foreach ($this->fieldGroups as $group) {
            if ($group->hasField($code)) {
                $field = $group->updateField($code, $name, $flags, $constraints, $configuration, $parent);

                if ($field) {
                    $this->recordThat(new FieldUpdated($this->code, $field->getCode()));
                    $this->recordUpdate();
                }
            }
        }
    }

    public function removeField(string $code): void
    {
        foreach ($this->fieldGroups as $group) {
            if ($group->hasField($code)) {
                $group->removeField($code);
                $this->recordThat(new FieldRemoved($this->code, $code));
                $this->recordUpdate();
            }
        }
    }

    public function sortFields(array $fieldsCodes): void
    {
        if ($fieldsCodes === []) {
            return;
        }

        foreach ($this->fieldGroups as $group) {
            $group->sortFields($fieldsCodes);
        }

        $newPositions = [[]];

        foreach ($this->fieldGroups as $group) {
            $newPositions[] = $group->getFieldsCodes();
        }

        $this->recordThat(new FieldsSorted($this->code, array_merge(...$newPositions)));
        $this->recordUpdate();
    }

    public function sortFieldsGroups(array $groupsCodes): void
    {
        $position = 1;

        foreach ($groupsCodes as $code) {
            if (isset($this->fieldGroups[$code])) {
                $this->fieldGroups[$code]->moveToPosition($position++);
            }
        }

        usort($this->fieldGroups, function (FieldsGroup $group1, FieldsGroup $group2) {
            return $group1->getPosition() <=> $group2->getPosition();
        });

        $newPositions = [];

        foreach ($this->fieldGroups as $group) {
            $newPositions[] = $group->getCode();
        }

        $this->recordThat(new FieldsGroupsSorted($this->code, $newPositions));
        $this->recordUpdate();
    }

    private function recordUpdate(): void
    {
        if (!$this->updateRecorded) {
            $this->recordThat(new ContentTypeUpdated($this->code));
            $this->updateRecorded = true;
        }
    }
}
