<?php

declare(strict_types=1);

namespace Tulia\Cms\Taxonomy\Domain\WriteModel\Model;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
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
    private bool $isRoot = false;
    /** @var ArrayCollection<int, TermTranslation> */
    private Collection $translations;
    /** @var ArrayCollection<int, Term> */
    private Collection $terms;

    private function __construct(
        private string $id,
        private ?Taxonomy $taxonomy,
        string $name,
        array $locales,
        string $creatingLocale,
        private ?Term $parent = null,
    ) {
        if ($this->id === self::ROOT_ID) {
            $this->isRoot = true;
        }

        $this->level = $this->parent ? $this->parent->level + 1 : 0;

        $translations = [];

        foreach ($locales as $locale) {
            $translations[] = new TermTranslation($this, $locale, false, $name, $creatingLocale);
        }

        $this->terms = new ArrayCollection();
        $this->translations = new ArrayCollection($translations);
    }

    public static function createRoot(Taxonomy $taxonomy, array $locales, string $creatingLocale): self
    {
        return new self(self::ROOT_ID, $taxonomy, 'root', $locales, $creatingLocale);
    }

    public static function create(string $id, Taxonomy $taxonomy, string $name, array $locales, string $creatingLocale, ?Term $parent = null): self
    {
        return new self($id, $taxonomy, $name, $locales, $creatingLocale, $parent);
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
