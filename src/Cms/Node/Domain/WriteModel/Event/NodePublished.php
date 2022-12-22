<?php

declare(strict_types=1);

namespace Tulia\Cms\Node\Domain\WriteModel\Event;

use Tulia\Cms\Shared\Domain\WriteModel\Model\ValueObject\ImmutableDateTime;

/**
 * @author Adam Banaszkiewicz
 */
final class NodePublished extends AbstractNodeDomainEvent
{
    public function __construct(
        string $id,
        string $type,
        public readonly ImmutableDateTime $publishedAt,
        public readonly ?ImmutableDateTime $publishedTo,
    ) {
        parent::__construct($id, $type);
    }

    public function isPublishedToForever(): bool
    {
        return $this->publishedTo === null;
    }
}
