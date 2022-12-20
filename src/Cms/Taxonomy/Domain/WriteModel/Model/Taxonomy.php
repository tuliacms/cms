<?php

declare(strict_types=1);

namespace Tulia\Cms\Taxonomy\Domain\WriteModel\Model;

use Doctrine\Common\Collections\Collection;
use Doctrine\Common\Collections\ArrayCollection;
use Tulia\Cms\Shared\Domain\WriteModel\Model\AbstractAggregateRoot;
use Tulia\Cms\Shared\Domain\WriteModel\Service\SlugGeneratorStrategy\SlugGeneratorStrategyInterface;
use Tulia\Cms\Taxonomy\Domain\WriteModel\Event\TermCreated;
use Tulia\Cms\Taxonomy\Domain\WriteModel\Event\TermDeleted;
use Tulia\Cms\Taxonomy\Domain\WriteModel\Event\TermTranslated;
use Tulia\Cms\Taxonomy\Domain\WriteModel\Event\TermUpdated;
use Tulia\Cms\Taxonomy\Domain\WriteModel\Exception\TermNotFoundException;

/**
 * @author Adam Banaszkiewicz
 */
class Taxonomy extends AbstractAggregateRoot
{
    private readonly string $id;
    private readonly string $type;
    private readonly string $websiteId;

    /** @var ArrayCollection<int, Term>|Term[] */
    public Collection $terms;

    private function __construct(string $type, string $websiteId, array $locales, string $locale)
    {
        $this->type = $type;
        $this->websiteId = $websiteId;
        $this->terms = new ArrayCollection();
        $this->terms->add(Term::createRoot($this, $locales, $locale));
    }

    public static function create(string $type, string $websiteId, array $locales, string $locale): self
    {
        return new self($type, $websiteId, $locales, $locale);
    }

    public function getType(): string
    {
        return $this->type;
    }

    public function termToArray(string $id, string $locale): array
    {
        return $this->getTerm($id)->toArray($locale);
    }

    public function addTerm(
        SlugGeneratorStrategyInterface $slugGeneratorStrategy,
        string $id,
        string $name,
        ?string $slug,
        array $locales,
        string $locale,
        string $defaultLocale,
        array $attributes = [],
        ?string $parent = null,
    ): void {
        if ($parent) {
            $parentTerm = $this->getTerm($parent);
        } else {
            $parentTerm = $this->getTerm(Term::ROOT_ID);
        }

        $term = Term::create($slugGeneratorStrategy, $id, $this, $name, $slug, $locales, $locale, $this->websiteId, parent: $parentTerm);

        if ($attributes !== []) {
            $term->persistAttributes($locale, $defaultLocale, $attributes);
        }

        $this->terms->add($term);
        $this->recordThat(new TermCreated($id, $this->type, $this->websiteId, $parent));
    }

    public function updateTerm(
        SlugGeneratorStrategyInterface $slugGeneratorStrategy,
        string $id,
        string $locale,
        string $name,
        ?string $slug,
        string $defaultLocale,
        array $attributes = [],
    ): void {
        $term = $this->getTerm($id);

        if ($attributes !== []) {
            $term->persistAttributes($locale, $defaultLocale, $attributes);
        }

        $slug = $slugGeneratorStrategy->generateGloballyUnique($term->getId(), $slug, $name, $this->websiteId, $locale);

        $translation = $term->getTranslation($locale);
        $translation->rename($name, $slug);

        $this->recordThat(new TermUpdated($id, $this->type, $this->websiteId));

        if ($translation->hasBeenTranslatedRightNow()) {
            $this->recordThat(new TermTranslated($id, $this->type, $this->websiteId, $locale));
        }
    }

    public function deleteTerm(string $id): void
    {
        foreach ($this->terms as $term) {
            if ($term->getId() === $id) {
                $this->terms->removeElement($term);
                $term->detach();
                $this->recordThat(new TermDeleted($term->getId(), $this->type, $this->websiteId));
            }
        }
    }

    private function getTerm(string $parent): Term
    {
        foreach ($this->terms as $term) {
            if ($term->getId() === $parent) {
                return $term;
            }
        }

        throw TermNotFoundException::fromId($parent);
    }
}
