<?php

declare(strict_types=1);

namespace Tulia\Cms\Node\Domain\WriteModel\Model;

/**
 * @author Adam Banaszkiewicz
 * @final
 */
class Term
{
    public const TYPE_MAIN = 'main';
    public const TYPE_ADDITIONAL = 'additional';
    public const TYPE_CALCULATED = 'calculated';

    private string $id;

    public function __construct(
        private ?Node $node,
        public readonly string $term,
        public readonly string $taxonomy,
        public readonly string $type,
    ) {
    }

    public function detach(): void
    {
        $this->node = null;
    }
}
