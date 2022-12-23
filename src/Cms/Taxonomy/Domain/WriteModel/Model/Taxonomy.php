<?php

declare(strict_types=1);

namespace Tulia\Cms\Taxonomy\Domain\WriteModel\Model;

use Doctrine\Common\Collections\Collection;
use Doctrine\Common\Collections\ArrayCollection;
use Tulia\Cms\Shared\Domain\WriteModel\Model\AbstractAggregateRoot;
use Tulia\Cms\Shared\Domain\WriteModel\Service\SlugGeneratorStrategy\SlugGeneratorStrategyInterface;
use Tulia\Cms\Taxonomy\Domain\WriteModel\Event\TermCreated;
use Tulia\Cms\Taxonomy\Domain\WriteModel\Event\TermDeleted;
use Tulia\Cms\Taxonomy\Domain\WriteModel\Event\TermsHierarchyChanged;
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
            $parentTerm = $this->getRootTerm();
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

        if ($term->isRoot()) {
            throw new \LogicException('Cannot update root term.');
        }

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

    public function moveTermAsChildOf(string $childId, string $parentId): void
    {
        $this->getTerm($childId)->moveToNewParent($this->getTerm($parentId));

        $this->recordThat(new TermsHierarchyChanged($this->type, $this->websiteId, $this->collectTermsHierarchy()));
    }

    public function updateHierarchy(array $hierarchy): void
    {
        $root = $this->getRootTerm();
        $rebuildedHierarchy = [];

        foreach ($hierarchy as $child => $parentId) {
            $rebuildedHierarchy[$parentId ?: $root->getId()][] = $child;
        }

        // Reset all elements to be sure, when some of terms are not in new hierarchy,
        // those will be moved as roots.
        /** @var Term $term */
        foreach ($this->terms->toArray() as $term) {
            if (false === $term->isRoot()) {
                $term->moveToNewParent($root);
            }
        }

        foreach ($rebuildedHierarchy as $parentId => $items) {
            foreach ($items as $position => $id) {
                // We dont want to move a root element
                if ($root->getId() === $id) {
                    continue;
                }

                $term = $this->getTerm($id);
                $term->moveToNewParent($this->getTerm($parentId));
                $term->moveToPosition($position + 1);
                $this->detectParentReccurency($term);
            }
        }

        $this->recordThat(new TermsHierarchyChanged($this->type, $this->websiteId, $this->collectTermsHierarchy()));
    }

    public function deleteTerm(string $id): void
    {
        foreach ($this->terms as $term) {
            if (!$term->isRoot() && $term->getId() === $id) {
                $this->terms->removeElement($term);
                $term->detach();
                $this->recordThat(new TermDeleted($term->getId(), $this->type, $this->websiteId));
            }
        }
    }

    private function getTerm(string $id): Term
    {
        foreach ($this->terms as $term) {
            if ($term->getId() === $id) {
                return $term;
            }
        }

        throw TermNotFoundException::fromId($id);
    }

    private function getRootTerm(): Term
    {
        foreach ($this->terms as $term) {
            if ($term->isRoot()) {
                return $term;
            }
        }

        throw TermNotFoundException::fromName('root');
    }

    private function collectTermsHierarchy(): array
    {
        $hierarchy = [];

        foreach ($this->terms->toArray() as $term) {
            if ($term->isRoot()) {
                continue;
            }

            $hierarchy[$term->getId()] = [];

            foreach ($term->terms->toArray() as $child) {
                $hierarchy[$term->getId()][] = $child->getId();
            }
        }

        return $hierarchy;
    }

    /**
     * @throws ParentItemReccurencyException
     */
    private function detectParentReccurency(Term $term): void
    {
        // @todo Implement recurrency detection.
    }
}
