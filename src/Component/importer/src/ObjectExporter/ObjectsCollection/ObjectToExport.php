<?php

declare(strict_types=1);

namespace Tulia\Component\Importer\ObjectExporter\ObjectsCollection;

/**
 * @author Adam Banaszkiewicz
 */
final class ObjectToExport
{
    public readonly string $group;

    /**
     * - Type is a global type of object, that diferentiates it. I.e. Node, Term, File, Form
     * - Group is a local box for objects in one type. I.e. Page and Product are groups of Node type.
     *   Category and Tag are groups of Term type.
     */
    public function __construct(
        public readonly string $type,
        public readonly string $id,
        public readonly string $name,
        ?string $group = null,
    ) {
        if (!$group) {
            $this->group = $this->type;
        } else {
            $this->group = $group;
        }
    }
}
