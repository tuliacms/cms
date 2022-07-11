<?php

declare(strict_types=1);

namespace Tulia\Cms\Node\Domain\WriteModel\Event;

use Tulia\Cms\Shared\Domain\WriteModel\Model\ValueObject\ImmutableDateTime;

/**
 * @author Adam Banaszkiewicz
 */
final class NodePublished extends AbstractDomainEvent
{
    public readonly ImmutableDateTime $publishedAt;

    public function __construct(string $id, string $type, ImmutableDateTime $publishedAt)
    {
        parent::__construct($id, $type);

        $this->publishedAt = $publishedAt;
    }
}
