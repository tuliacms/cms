<?php

declare(strict_types=1);

namespace Tulia\Cms\SearchAnything\Domain\WriteModel\Service;

use Tulia\Cms\SearchAnything\Domain\WriteModel\Enum\LocalizationStrategyEnum;
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
    public function getCollector(): DocumentCollectorInterface;
    public function isMultisiteStrategy(MultisiteStrategyEnum $type): bool;
    public function isLocalizationStrategy(LocalizationStrategyEnum $type): bool;
    public function getName(): string;
}
