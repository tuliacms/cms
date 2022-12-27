<?php

declare(strict_types=1);

namespace Tulia\Cms\Taxonomy\Domain\ReadModel\Routing\Strategy;

use Psr\Log\LoggerInterface;
use Symfony\Contracts\Cache\TagAwareCacheInterface;
use Tulia\Cms\Content\Type\Domain\ReadModel\Model\ContentType;
use Tulia\Cms\Content\Type\Domain\ReadModel\Service\ContentTypeRegistryInterface;
use Tulia\Cms\Content\Type\Domain\WriteModel\Routing\Strategy\ContentTypeRoutingStrategyInterface;
use Tulia\Cms\Taxonomy\Domain\ReadModel\Finder\TermFinderInterface;
use Tulia\Cms\Taxonomy\Domain\ReadModel\Finder\TermFinderScopeEnum;
use Tulia\Cms\Taxonomy\Domain\ReadModel\Model\Term;
use Tulia\Cms\Taxonomy\Domain\ReadModel\Service\TermPathReadStorageInterface;

/**
 * @author Adam Banaszkiewicz
 */
abstract class AbstractRoutingStrategy implements ContentTypeRoutingStrategyInterface
{
    public function __construct(
        protected readonly TermPathReadStorageInterface $storage,
        protected readonly TermFinderInterface $termFinder,
        protected readonly ContentTypeRegistryInterface $contentTypeRegistry,
        protected readonly LoggerInterface $logger,
        protected readonly TagAwareCacheInterface $taxonomyCache,
    ) {
    }

    /**
     * Generate method must be implemented individually by all Routing Strategies, and must not
     * gets data from the flat table (#__term_path). Paths generated by this method goes directly
     * to flat table when new term is created or other updated.
     */
    abstract public function generate(string $id, array $parameters = []): string;

    abstract public function collectVisibleTermsGrouppedByTaxonomy(string $websiteId, string $locale): array;

    /**
     * Matching is done by fetching data from flat cached view table (#__term_path).
     * Data goes there when term is saved. Generation is done by the RoutingStrategy::generate()
     * method. So:
     * - Matching is done the same by all Routing Strategies
     * - Generating is done individually by each Routing Strategy.
     */
    public function match(string $pathinfo, array $parameters = []): array
    {
        $terms = $this->collectVisibleTermsGrouppedByTaxonomy($parameters['_website'], $parameters['_locale']);

        $termId = $this->findTermIdByPath($terms, $pathinfo);

        if ($termId === null) {
            return [];
        }

        /** @var Term $term */
        $term = $this->getTerm($termId, $parameters['_website'], $parameters['_locale']);

        if ($this->isTermRoutable($term, $termType) === false) {
            $this->logger->info('Taxonomy type not exists or is not routable.');
            return [];
        }

        return [
            'term' => $term,
            'slug' => $term->getSlug(),
            '_route' => sprintf('frontend.term.%s.%s', 'category', $term->getId()),
            '_controller' => $termType->getController(),
        ];
    }

    private function findTermIdByPath(array $terms, string $path): ?string
    {
        foreach ($terms as $term) {
            if ($term['path'] === $path) {
                return $term['id'];
            }
        }

        return null;
    }

    private function isTermRoutable(?Term $term, ?ContentType &$contentType): bool
    {
        if (! $term instanceof Term) {
            return false;
        }

        $contentType = $this->contentTypeRegistry->get($term->getType());

        return $contentType && $contentType->isRoutable();
    }

    private function getTerm(string $id, string $websiteId, string $locale): ?Term
    {
        return $this->termFinder->findOne([
            'id'            => $id,
            'per_page'      => 1,
            'order_by'      => null,
            'order_dir'     => null,
            'visibility'    => 1,
            'taxonomy_type' => null,
            'website_id'    => $websiteId,
            'locale'        => $locale,
        ], TermFinderScopeEnum::ROUTING_MATCHER);
    }
}
