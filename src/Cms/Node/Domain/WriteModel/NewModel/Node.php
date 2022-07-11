<?php

declare(strict_types=1);

namespace Tulia\Cms\Node\Domain\WriteModel\NewModel;

use Doctrine\Common\Collections\Collection;
use Tulia\Cms\Node\Domain\WriteModel\Event;
use Tulia\Cms\Node\Domain\WriteModel\Exception\NodeTranslationDoesntExists;
use Tulia\Cms\Shared\Domain\WriteModel\Model\AbstractAggregateRoot;
use Tulia\Cms\Shared\Domain\WriteModel\Model\ValueObject\ImmutableDateTime;

/**
 * @author Adam Banaszkiewicz
 * @final
 */
class Node extends AbstractAggregateRoot
{
    /** @var TranslatedNode[] */
    private Collection $translations;
    private ImmutableDateTime $publishedAt;
    private ?ImmutableDateTime $publishedTo = null;
    private ImmutableDateTime $createdAt;
    private ?ImmutableDateTime $updatedAt = null;
    /** @var Attribute[] */
    private Collection $attributes;
    private string $status = 'draft';
    private ?Node $parentNode = null;
    private int $level = 0;
    private ?string $slug = null;
    /** @var string[] */
    private array $purposes = [];

    private function __construct(
        private string $id,
        private string $type,
        private string $title,
        private string $author
    ) {
    }

    public static function create(
        string $id,
        string $type,
        string $title,
        string $author
    ): self {
        $self = new self($id, $type, $title, $author);
        $self->createdAt = new ImmutableDateTime();
        $self->recordThat(new Event\NodeCreated($id, $type));

        return $self;
    }

    public function trans(string $locale): TranslatedNode
    {
        foreach ($this->translations as $translation) {
            if ($translation->isFor($locale)) {
                return $translation;
            }
        }

        throw NodeTranslationDoesntExists::fromLocale($this->id, $locale);
    }

    public function isTranslatedTo(string $locale): bool
    {
        foreach ($this->translations as $translation) {
            if ($translation->isFor($locale)) {
                return true;
            }
        }

        return false;
    }

    public function translate(string $locale, string $title): TranslatedNode
    {
        $translation = new TranslatedNode($this, $locale, $title);

        $this->translations->add($translation);

        return $translation;
    }

    public function publish(ImmutableDateTime $publishedAt): void
    {
        $this->publishedAt = $publishedAt;

        $this->recordThat(new Event\NodePublished($this->id, $this->type, $this->publishedAt));
    }
}
