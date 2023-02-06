<?php

declare(strict_types=1);

namespace Tulia\Component\Importer\Structure;

use Tulia\Component\Importer\Schema\Schema;

/**
 * @author Adam Banaszkiewicz
 */
final class ObjectDataFactory
{
    public function __construct(
        private readonly Schema $schema,
    ) {
    }

    public function createEmpty(string $type, ?string $id = null): ObjectData
    {
        $data = ['@type' => $type];

        if ($id) {
            $data['@id'] = $id;
        }

        return new ObjectData($data, $this->schema->get($type), '');
    }

    public function create(string $type, array $source): ObjectData
    {
        $source['@type'] = $type;

        return new ObjectData($source, $this->schema->get($type), '');
    }
}
