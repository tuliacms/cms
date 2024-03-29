<?php

declare(strict_types=1);

namespace Tulia\Cms\Node\Domain\WriteModel\Model\NodeFeatures;

use Doctrine\Common\Collections\Collection;
use Tulia\Cms\Node\Domain\WriteModel\Model\Node;
use Tulia\Cms\Node\Domain\WriteModel\Model\Term;
use Tulia\Cms\Node\Domain\WriteModel\Service\ParentTermsResolverInterface;

/**
 * @author Adam Banaszkiewicz
 */
final class TermsFeature
{
    /**
     * @param Collection|Term[] $terms
     */
    public function __construct(
        private readonly Node $node,
        private readonly Collection $terms,
    ) {
    }

    public function assignToTermOf(ParentTermsResolverInterface $resolver, string $term, string $taxonomy, string $type): bool
    {
        if ($this->findAssignedTerm($term, $taxonomy)) {
            return false;
        }

        $this->terms->add(new Term($this->node, $term, $taxonomy, $type));

        $this->calculateParentTerms($resolver, $term, $taxonomy);

        return true;
    }

    public function unassignFromTermOf(ParentTermsResolverInterface $resolver, string $term, string $taxonomy, string $type): bool
    {
        $termEntity = $this->findAssignedTerm($term, $taxonomy);

        if (!$termEntity) {
            return false;
        }

        // 1: Remove main term
        $this->terms->removeElement($termEntity);
        $termEntity->detach();

        // 2: Remove autoassigned terms (which also removes autoassignes of main term)
        /** @var Term $element */
        foreach ($this->terms->toArray() as $element) {
            if ($element->type === Term::TYPE_CALCULATED) {
                $this->terms->removeElement($element);
                $element->detach();
            }
        }

        // 3: Calculate once again all parents of left (main) terms
        foreach ($this->terms->toArray() as $element) {
            $this->calculateParentTerms($resolver, $element->term, $element->taxonomy);
        }

        return true;
    }

    public function collectTermsAssignations(): array
    {
        $result = [];

        /** @var Term $term */
        foreach ($this->terms->toArray() as $term) {
            $result[] = [
                'term' => $term->term,
                'taxonomy' => $term->taxonomy,
                'type' => $term->type,
            ];
        }

        return $result;
    }

    public function unassignFromAllTerms(ParentTermsResolverInterface $resolver, string $type): void
    {
        foreach ($this->terms as $term) {
            if ($term->type === $type || $term->type === Term::TYPE_CALCULATED) {
                $term->detach();
                $this->terms->removeElement($term);
            }
        }

        foreach ($this->terms->toArray() as $element) {
            $this->calculateParentTerms($resolver, $element->term, $element->taxonomy);
        }
    }

    public function persistAdditionalCategoriesAssignations(ParentTermsResolverInterface $resolver, array ...$terms): bool
    {
        $newTermsByTaxonomy = [];
        foreach ($terms as $term) {
            if (count($term) === 2) {
                $newTermsByTaxonomy[$term[1]][$term[0]] = $term[0];
            }
        }

        if (empty($newTermsByTaxonomy)) {
            return false;
        }

        $oldTermsByTaxonomy = [];
        /** @var Term $term */
        foreach ($this->terms->toArray() as $term) {
            if ($term->type !== Term::TYPE_ADDITIONAL) {
                continue;
            }

            $oldTermsByTaxonomy[$term->taxonomy][$term->term] = $term->term;
        }

        $toAdd = [];
        $toRemove = [];

        // 1: remove old terms, that are not in new terms
        foreach ($oldTermsByTaxonomy as $oldTaxonomy => $oldTerms) {
            foreach ($oldTerms as $oldTerm) {
                $found = false;

                foreach ($newTermsByTaxonomy as $newTaxonomy => $newTerms) {
                    if ($oldTaxonomy === $newTaxonomy) {
                        if (isset($newTermsByTaxonomy[$newTaxonomy][$oldTerm])) {
                            // Terms both in old an new lists are not changed,
                            // so we dont do anything with those.
                            unset($newTermsByTaxonomy[$newTaxonomy][$oldTerm]);
                            $found = true;
                            break;
                        }
                    }
                }

                if (false === $found) {
                    $toRemove[$oldTaxonomy][$oldTerm] = $oldTerm;
                }
            }
        }

        // 2: Elements that left in new array, are those new which we have to add
        $toAdd = $newTermsByTaxonomy;

        if (empty($toAdd) && empty($toRemove)) {
            return false;
        }

        // 3: Remove old terms
        foreach ($toRemove as $taxonomy => $termsToRemove) {
            foreach ($termsToRemove as $termToRemove) {
                $this->unassignFromTermOf($resolver, $termToRemove, $taxonomy, Term::TYPE_ADDITIONAL);
            }
        }

        // 4: Add new terms
        foreach ($toAdd as $taxonomy => $termsToAdd) {
            foreach ($termsToAdd as $termToAdd) {
                $this->assignToTermOf($resolver, $termToAdd, $taxonomy, Term::TYPE_ADDITIONAL);
            }
        }

        return true;
    }

    public function unassignFromMainCategory(ParentTermsResolverInterface $resolver): bool
    {
        foreach ($this->terms as $term) {
            if ($term->type === Term::TYPE_MAIN) {
                $this->unassignFromTermOf($resolver, $term->term, $term->taxonomy, Term::TYPE_MAIN);
                return true;
            }
        }

        return false;
    }

    private function findAssignedTerm(string $term, string $taxonomy): ?Term
    {
        /** @var Term $pretendend */
        foreach ($this->terms->toArray() as $pretendend) {
            if ($pretendend->term === $term && $pretendend->taxonomy === $taxonomy) {
                return $pretendend;
            }
        }

        return null;
    }

    private function calculateParentTerms(ParentTermsResolverInterface $resolver, string $term, string $taxonomy): void
    {
        foreach ($resolver->fetchAllParents($term, $taxonomy, $this->node->getWebsiteId()) as $parentTerm) {
            $this->terms->add(new Term($this->node, $parentTerm, $taxonomy, Term::TYPE_CALCULATED));
        }
    }
}
