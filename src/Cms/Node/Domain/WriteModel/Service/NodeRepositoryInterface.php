<?php

declare(strict_types=1);

namespace Tulia\Cms\Node\Domain\WriteModel\Service;

use Tulia\Cms\Node\Domain\WriteModel\Model\Node;

/**
 * @author Adam Banaszkiewicz
 */
interface NodeRepositoryInterface
{
    public function createNew(string $nodeType, string $author, string $locale): Node;

    public function find(string $id): ?Node;

    public function insert(Node $node): void;

    public function update(Node $node): void;

    public function delete(Node $node): void;
}
