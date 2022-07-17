<?php

declare(strict_types=1);

namespace Tulia\Cms\Node\Domain\WriteModel\Service;

use Tulia\Cms\Node\Domain\WriteModel\NewModel\Node;

/**
 * @author Adam Banaszkiewicz
 */
interface NodeRepositoryInterface
{
    public function create(string $nodeType, string $author): Node;

    public function referenceTo(string $id): Node;

    public function get(string $id): Node;

    public function save(Node $node);

    public function delete(Node $node): void;
}
