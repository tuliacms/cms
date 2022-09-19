<?php

declare(strict_types=1);

namespace Tulia\Cms\Node\Domain\WriteModel\Service;

use Tulia\Cms\Node\Domain\WriteModel\Model\Node;

/**
 * @author Adam Banaszkiewicz
 */
interface NodeRepositoryInterface
{
    public function create(
        string $nodeType,
        string $author,
        string $title,
        string $websiteId,
        array $availableLocales,
    ): Node;

    public function referenceTo(string $id): Node;

    public function get(string $id): Node;

    public function save(Node $node);

    public function delete(Node $node): void;
}
