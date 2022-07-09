<?php

declare(strict_types=1);

namespace Tulia\Cms\Node\Domain\WriteModel\Event;

/**
 * @author Adam Banaszkiewicz
 */
final class PurposesUpdated extends AbstractDomainEvent
{
    public function __construct(
        string $nodeId,
        string $nodeType,
        string $locale,
        public readonly array $purposes
    ) {
        parent::__construct($nodeId, $nodeType, $locale);
    }
}
