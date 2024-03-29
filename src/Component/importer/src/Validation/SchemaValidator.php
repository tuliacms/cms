<?php

declare(strict_types=1);

namespace Tulia\Component\Importer\Validation;

use Tulia\Component\Importer\Exception\EmptyValueOfRequiredFieldException;
use Tulia\Component\Importer\Exception\InvalidFieldDataTypeException;
use Tulia\Component\Importer\Schema\Field;
use Tulia\Component\Importer\Schema\ObjectDefinition;
use Tulia\Component\Importer\Schema\Schema;
use Tulia\Component\Importer\Structure\ObjectData;

use function is_array;
use function is_int;
use function is_string;

/**
 * @author Adam Banaszkiewicz
 */
class SchemaValidator implements SchemaValidatorInterface
{
    public function __construct(
        private readonly Schema $schema,
    ) {
    }

    public function validate(array $objects, string $importRootPath): array
    {
        foreach ($objects as $key => $object) {
            $objects[$key] = $this->validateObject($object, 'objects.'.$key, $importRootPath);
        }

        return array_filter($objects);
    }

    /**
     * @throws InvalidFieldDataTypeException
     * @throws EmptyValueOfRequiredFieldException
     */
    private function validateObject(array $objectData, string $path, string $importRootPath): ?ObjectData
    {
        \assert(isset($objectData['@type']), 'Missing @type key of imported Object data at path "'.$path.'".');

        if ($this->schema->has($objectData['@type']) === false) {
            return null;
        }

        $object = $this->schema->get($objectData['@type']);

        foreach ($objectData as $key => $val) {
            if ($key[0] === '@') {
                continue;
            }

            if ($object->hasField($key) === false) {
                unset($objectData[$key]);
                continue;
            }

            $objectData[$key] = $this->validateField(
                $object,
                $object->getField($key),
                $objectData[$key],
                $path.'.'.$key,
                $importRootPath
            );
        }

        foreach ($object->getFields() as $field) {
            if (isset($objectData[$field->getName()])) {
                continue;
            }

            $objectData[$field->getName()] = $this->validateField(
                $object,
                $field,
                $field->getDefaultValue(),
                $path.'.'.$field->getName(),
                $importRootPath
            );
        }

        return new ObjectData($objectData, $object, $importRootPath);
    }

    /**
     * @throws InvalidFieldDataTypeException
     * @throws EmptyValueOfRequiredFieldException
     */
    private function validateField(ObjectDefinition $object, Field $field, mixed $data, string $path, string $importRootPath): mixed
    {
        if ($this->isEmpty($data)) {
            if ($field->isRequired()) {
                throw EmptyValueOfRequiredFieldException::fromField($object, $field, $path);
            }

            // Do nothing if is empty and not required.
            return null;
        }

        switch ($field->getType()) {
            case 'string':
                if (is_string($data) === false) {
                    throw InvalidFieldDataTypeException::fromField($object, $field, $data, $path);
                }
                break;
            case 'boolean':
                if (is_bool($data) === false) {
                    throw InvalidFieldDataTypeException::fromField($object, $field, $data, $path);
                }
                break;
            case 'integer':
                if (is_int($data) === false) {
                    throw InvalidFieldDataTypeException::fromField($object, $field, $data, $path);
                }
                break;
            case 'scalar':
                if (is_scalar($data) === false) {
                    throw InvalidFieldDataTypeException::fromField($object, $field, $data, $path);
                }
                break;
            case 'number':
                if (is_numeric($data) === false) {
                    throw InvalidFieldDataTypeException::fromField($object, $field, $data, $path);
                }
                break;
            case 'array':
                if (is_array($data) === false) {
                    throw InvalidFieldDataTypeException::fromField($object, $field, $data, $path);
                }
                break;
            case 'one_dimension_array':
                if (is_array($data) === false) {
                    foreach ($data as $val) {
                        if (is_array($val)) {
                            throw InvalidFieldDataTypeException::fromField($object, $field, $data, $path);
                        }
                    }
                }
                break;
            case 'uuid':
                if (preg_match('#^[0-9a-fA-F]{8}\-[0-9a-fA-F]{4}\-[0-9a-fA-F]{4}\-[0-9a-fA-F]{4}\-[0-9a-fA-F]{12}$#', $data) === false) {
                    if (is_array($data)) {
                        throw InvalidFieldDataTypeException::fromField($object, $field, $data, $path);
                    }
                }
                break;
            case 'datetime':
                if (preg_match('#^\d{4}-\d{2}-\d{2}\s\d{2}:\d{2}:\d{2}$#', $data) === false) {
                    if (is_array($data)) {
                        throw InvalidFieldDataTypeException::fromField($object, $field, $data, $path);
                    }
                }
                break;
            default:
                if ($this->schema->has($field->getType()) === false) {
                    throw InvalidFieldDataTypeException::fromField($object, $field, $data, $path);
                }

                foreach ($data as $key => $val) {
                    $data[$key] = $this->validateObject($val, $path.'.'.$key, $importRootPath);
                }
        }

        return $data;
    }

    private function isEmpty(mixed $data): bool
    {
        return $data === null || $data === '';
    }
}
