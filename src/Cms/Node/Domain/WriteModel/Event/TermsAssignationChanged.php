<?php

declare(strict_types=1);

namespace Tulia\Cms\Node\Domain\WriteModel\Event;

/**
 * @author Adam Banaszkiewicz
 */
final class TermsAssignationChanged extends AbstractNodeDomainEvent
{
    public function __construct(
        string $id,
        string $type,
        private readonly array $terms,
    ) {
        parent::__construct($id, $type);
    }

    public function isAssignedTo(string $term, string $taxonomy): bool
    {
        foreach ($this->terms as $pretendent) {
            if ($pretendent['term'] === $term && $pretendent['taxonomy'] === $taxonomy) {
                return true;
            }
        }

        return false;
    }
}
