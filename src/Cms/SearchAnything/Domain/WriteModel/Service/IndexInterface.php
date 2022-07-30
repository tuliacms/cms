<?php

declare(strict_types=1);

namespace Tulia\Cms\SearchAnything\Domain\WriteModel\Service;

use Tulia\Cms\SearchAnything\Domain\WriteModel\Model\Document;

/**
 * @author Adam Banaszkiewicz
 */
interface IndexInterface
{
    public function document(string $sourceId): Document;
    public function save(Document $document): void;
    public function clear(): void;
    public function delete(Document $document): void;
    public function getDelta(): int;
}
