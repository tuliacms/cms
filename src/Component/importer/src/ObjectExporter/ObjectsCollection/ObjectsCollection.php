<?php

declare(strict_types=1);

namespace Tulia\Component\Importer\ObjectExporter\ObjectsCollection;

/**
 * @author Adam Banaszkiewicz
 */
final class ObjectsCollection
{
    /**
     * @var ObjectToExport[]
     */
    private array $objects = [];

    public function addObject(ObjectToExport $object): void
    {
        $this->objects[] = $object;
    }

    public function getTypes(): array
    {
        return array_values(array_unique(array_map(static fn($v) => $v->type, $this->objects)));
    }

    public function getGroupsOfType(string $type): array
    {
        $ofType = array_filter($this->objects, static fn($v) => $v->type === $type);

        return array_values(array_unique(array_map(static fn($v) => $v->group, $ofType)));
    }

    public function of(string $group, ?string $type = null): array
    {
        if (!$type) {
            $type = $group;
        }

        return array_values(array_filter($this->objects, static fn($v) => $v->type === $type && $v->group === $group));
    }
}
