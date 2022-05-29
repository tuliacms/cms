<?php

declare(strict_types=1);

namespace Tulia\Cms\Content\Type\Domain\WriteModel\Model;

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

    /**
     * @throws ParentFieldNotExistsException
     */
    public function addField(Field $field): void
    {
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

        $this->fields[] = $field;
    }
}
