<?php

declare(strict_types=1);

namespace Tulia\Cms\Node\Domain\WriteModel\NewModel;

use Doctrine\Common\Collections\Collection;

/**
 * @author Adam Banaszkiewicz
 */
final class TranslatedNode
{
    private string $id;
    /** @var Attribute[] */
    private Collection $attributes;
    private ?string $slug = null;

    public function __construct(
        private Node $node,
        private string $locale,
        private string $title
    ) {
    }

    public function isFor(string $locale): bool
    {
        return $locale === $this->locale;
    }
}
