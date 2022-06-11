<?php

declare(strict_types=1);

namespace Tulia\Cms\Content\Type\Domain\WriteModel\Model;

use Tulia\Cms\Content\Type\Domain\WriteModel\Exception\FieldWithThatCodeAlreadyExistsException;
use Tulia\Cms\Content\Type\Domain\WriteModel\Exception\ParentFieldNotExistsException;

/**
 * @author Adam Banaszkiewicz
 */
final class FieldsGroup
{
    private string $code;
    private string $section;
    private string $name;
    /** @var Field[] */
    private array $fields = [];

    public function __construct(string $code, string $section, string $name)
    {
        $this->code = $code;
        $this->section = $section;
        $this->name = $name;
    }

    public function toArray(): array
    {
        return [
            'code' => $this->code,
            'section' => $this->section,
            'name' => $this->name,
            'fields' => array_map(
                fn (Field $field) => $field->toArray(),
                $this->fields
            ),
        ];
    }

    public function getFieldsCodes(): array
    {
        return array_map(fn ($f) => $f->getCode(), $this->fields);
    }

    public function getCode(): string
    {
        return $this->code;
    }

    public function rename(string $name): bool
    {
        if ($this->name !== $name) {
            $this->name = $name;
            return true;
        }

        return false;
    }

    /**
     * @throws ParentFieldNotExistsException
     * @throws FieldWithThatCodeAlreadyExistsException
     */
    public function addField(Field $field): void
    {
        if (isset($this->fields[$field->getCode()])) {
            throw FieldWithThatCodeAlreadyExistsException::fromCode($field->getCode());
        }

        if ($field->getParent()) {
            $found = false;

            foreach ($this->fields as $pretendendToParent) {
                if ($pretendendToParent->getCode() === $field->getParent()) {
                    $found = true;
                    break;
                }
            }

            if (!$found) {
                throw ParentFieldNotExistsException::fromName($field->getParent());
            }
        }

        $this->fields[$field->getCode()] = $field;
    }

    public function updateField(
        string $code,
        string $name,
        array $flags = [],
        array $constraints = [],
        array $configuration = [],
        ?string $parent = null,
        ?int $position = null
    ): ?Field {
        $currentField = null;
        $currentFieldPosition = null;

        foreach ($this->fields as $key => $field) {
            if ($field->getCode() === $code) {
                $currentField = $field;
                $currentFieldPosition = $key;
            }
        }

        if (!$currentField) {
            throw new \OutOfBoundsException(sprintf('Field %s not exists, cannot update.', $code));
        }

        $newField = new Field($code, $currentField->getType(), $name, $flags, $constraints, $configuration, $parent, $position ?? $currentField->getPosition());

        if (!$newField->sameAs($currentField)) {
            $this->fields[$currentFieldPosition] = $newField;
            return $newField;
        }

        return null;
    }

    public function removeField(string $code): void
    {
        unset($this->fields[$code]);
    }

    public function hasField(string $code): bool
    {
        return isset($this->fields[$code]);
    }

    public function sortFields(array $fieldsCodes): void
    {
        $position = 1;

        foreach ($fieldsCodes as $code) {
            if (isset($this->fields[$code])) {
                $this->fields[$code]->moveToPosition($position++);
            }
        }

        usort($this->fields, function (Field $field1, Field $field2) {
            return $field1->getPosition() <=> $field2->getPosition();
        });
    }
}
