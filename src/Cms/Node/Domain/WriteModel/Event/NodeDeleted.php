<?php

declare(strict_types=1);

namespace Tulia\Cms\Node\Domain\WriteModel\Event;

/**
 * @author Adam Banaszkiewicz
 */
class NodeDeleted extends AbstractDomainEvent
{
    public function __construct(
        string $id,
        string $type,
        public readonly array $translatedTo
    ) {
        parent::__construct($id, $type);
    }
}
