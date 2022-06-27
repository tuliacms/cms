<?php

declare(strict_types=1);

namespace Tulia\Cms\Taxonomy\Domain\WriteModel\Event;

use Tulia\Cms\Taxonomy\Domain\WriteModel\Model\Term;

/**
 * @author Adam Banaszkiewicz
 */
class TermCreated extends AbstractDomainEvent
{
    public static function fromTerm(Term $term): self
    {
        return new self($term->getId()->getValue(), $term->getTaxonomy()->getType(), $term->getTaxonomy()->getWebsiteId(), $term->getLocale());
    }
}
