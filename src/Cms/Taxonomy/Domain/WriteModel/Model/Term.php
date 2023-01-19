<?php

declare(strict_types=1);

namespace Tulia\Cms\Taxonomy\Domain\WriteModel\Model;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Symfony\Component\Uid\Uuid;
use Tulia\Cms\Shared\Domain\WriteModel\Service\SlugGeneratorStrategy\SlugGeneratorStrategyInterface;
use Tulia\Cms\Taxonomy\Domain\WriteModel\Exception\TermNotFoundException;

/**
 * @author Adam Banaszkiewicz
 */
class Term
{
    private int $position = 0;
    private int $level = 0;
    /** @var ArrayCollection<int, TermTranslation> */
    private Collection $translations;
    /** @var ArrayCollection<int, Term> */
    public Collection $terms;

    private function __construct(
        private string $id,
        private ?Taxonomy $taxonomy,
        private bool $isRoot = false,
        private ?Term $parent = null,
    ) {
        $this->terms = new ArrayCollection();
    }

    public static function createRoot(
        Taxonomy $taxonomy,
        array $locales,
        string $creatingLocale,
    ): self {
        /** @todo Move root ID generation to more apropriate place */
        $self = new self((string) Uuid::v4(), $taxonomy, true);

        $translations = [];

        foreach ($locales as $locale) {
            $translations[] = new TermTranslation($self, $locale, 'root', 'root', $creatingLocale);
        }

        $self->translations = new ArrayCollection($translations);

        return $self;
    }

    public static function create(
        SlugGeneratorStrategyInterface $slugGeneratorStrategy,
        string $id,
        Taxonomy $taxonomy,
        string $name,
        ?string $slug,
        array $locales,
        string $creatingLocale,
        string $websiteId,
        ?Term $parent = null,
    ): self {
        $self = new self($id, $taxonomy, false, $parent);
        $self->level = $self->parent
            ? $self->parent->level + 1
            : 0;

        $translations = [];

        foreach ($locales as $locale) {
            $slug = $slugGeneratorStrategy->generateGloballyUnique($self->getId(), $slug, $name, $websiteId, $locale);
            $translations[] = new TermTranslation($self, $locale, $name, $slug, $creatingLocale);
        }

        $self->translations = new ArrayCollection($translations);
        $self->position = $self->parent->calculateNextPosition();

        return $self;
    }

    public function getId(): string
    {
        return $this->id;
    }

    public function isRoot(): bool
    {
        return $this->isRoot;
    }

    public function detach(): void
    {
        if ($this->parent) {
            $this->parent->terms->removeElement($this);
            $this->parent = null;
        }

        $this->taxonomy = null;
    }

    public function toArray(string $locale): array
    {
        return $this->getTranslation($locale)->toArray();
    }

    public function persistAttributes(string $locale, string $defaultLocale, array $attributes)
    {
        $trans = $this->getTranslation($locale);
        $trans->persistAttributes(...$attributes);
        $trans->translated = true;

        if ($locale === $defaultLocale) {
            foreach ($this->translations as $translation) {
                if (false === $translation->isTranslated()) {
                    $translation->persistAttributes(...$attributes);
                }
            }
        }
    }

    public function turnVisibilityOff(string $locale, string $defaultLocale): bool
    {
        $trans = $this->getTranslation($locale);
        $trans->translated = true;
        $state = $trans->turnVisibilityOff();

        if ($locale === $defaultLocale) {
            foreach ($this->translations as $translation) {
                if (false === $translation->isTranslated()) {
                    $translation->turnVisibilityOff();
                }
            }
        }

        return $state;
    }

    public function turnVisibilityOn(string $locale, string $defaultLocale): bool
    {
        $trans = $this->getTranslation($locale);
        $state = $trans->turnVisibilityOn();

        if ($locale === $defaultLocale) {
            foreach ($this->translations as $translation) {
                if (false === $translation->isTranslated()) {
                    $translation->turnVisibilityOn();
                }
            }
        }

        return $state;
    }

    public function getTranslation(string $locale): TermTranslation
    {
        foreach ($this->translations as $translation) {
            if ($translation->locale === $locale) {
                return $translation;
            }
        }

        throw TermNotFoundException::fromId($this->id);
    }

    public function moveToNewParent(Term $newParent): void
    {
        $oldParent = $this->parent;

        $oldParent->terms->removeElement($this);
        $newParent->terms->add($this);

        $this->parent = $newParent;
        $this->level = $this->parent->level + 1;
        $this->position = $this->parent->calculateNextPosition();
    }

    public function moveToPosition(int $position): void
    {
        $this->position = $position;
    }

    public function getVisibilityInTranslations(): array
    {
        $result = [];

        foreach ($this->translations as $trans) {
            $result[$trans->locale] = $trans->visibility;
        }

        return $result;
    }

    private function calculateNextPosition(): int
    {
        $positions = [];

        foreach ($this->terms->toArray() as $term) {
            $positions[] = $term->position;
        }

        if (empty($positions)) {
            return 0;
        }

        return max($positions) + 1;
    }
}
