<?php

declare(strict_types=1);

namespace Tulia\Cms\SearchAnything\Domain\WriteModel\Service;

use Tulia\Cms\SearchAnything\Domain\WriteModel\Enum\MultisiteStrategyEnum;
use Tulia\Cms\SearchAnything\Domain\WriteModel\Model\Document;

/**
 * @author Adam Banaszkiewicz
 */
interface IndexInterface
{
    public function document(string $sourceId, string $webisteId = null, string $locale = null): Document;
    public function save(Document $document): void;
    public function clear(?string $webisteId = null, ?string $locale = null): void;
    public function delete(Document $document): void;
    public function getDelta(): int;
    public function isMultilingual(): bool;
    public function getCollector(): DocumentCollectorInterface;
    public function isLocalizationStrategy(MultisiteStrategyEnum $type): bool;
}
