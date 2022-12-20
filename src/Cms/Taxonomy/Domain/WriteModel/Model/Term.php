<?php

declare(strict_types=1);

namespace Tulia\Cms\Taxonomy\Domain\WriteModel\Model;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Tulia\Cms\Shared\Domain\WriteModel\Service\SlugGeneratorStrategy\SlugGeneratorStrategyInterface;
use Tulia\Cms\Taxonomy\Domain\WriteModel\Exception\TermNotFoundException;

/**
 * @author Adam Banaszkiewicz
 */
class Term
{
    public const ROOT_ID = '00000000-0000-0000-0000-000000000000';
    public const ROOT_LEVEL = 0;

    private int $position = 0;
    private int $level = 0;
    /** @var ArrayCollection<int, TermTranslation> */
    private Collection $translations;
    /** @var ArrayCollection<int, Term> */
    private Collection $terms;

    private function __construct(
        private string $id,
        private bool $isRoot = false,
        private ?Taxonomy $taxonomy,
        private ?Term $parent = null,
    ) {
        $this->terms = new ArrayCollection();
    }

    public static function createRoot(Taxonomy $taxonomy, array $locales, string $creatingLocale): self
    {
        $self = new self(self::ROOT_ID, true, $taxonomy);

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
        $self = new self($id, false, $taxonomy, $parent);
        $self->level = $self->parent
            ? $self->parent->level + 1
            : 0;

        $translations = [];

        foreach ($locales as $locale) {
            $slug = $slugGeneratorStrategy->generateGloballyUnique($self->getId(), $slug, $name, $websiteId, $locale);
            $translations[] = new TermTranslation($self, $locale, $name, $slug, $creatingLocale);
        }

        $self->translations = new ArrayCollection($translations);

        return $self;
    }

    public function getId(): string
    {
        return $this->id;
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

    public function getTranslation(string $locale): TermTranslation
    {
        foreach ($this->translations as $translation) {
            if ($translation->locale === $locale) {
                return $translation;
            }
        }

        throw TermNotFoundException::fromId($this->id);
    }
}
