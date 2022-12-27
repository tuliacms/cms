<?php

declare(strict_types=1);

namespace Tulia\Cms\Taxonomy\Domain\ReadModel\Routing\Strategy;

use Psr\Log\LoggerInterface;
use Symfony\Contracts\Cache\TagAwareCacheInterface;
use Tulia\Cms\Content\Type\Domain\ReadModel\Service\ContentTypeRegistryInterface;
use Tulia\Cms\Taxonomy\Domain\ReadModel\Finder\TermFinderInterface;
use Tulia\Cms\Taxonomy\Domain\ReadModel\Service\TermPathReadStorageInterface;
use Tulia\Cms\Taxonomy\Domain\WriteModel\Model\Term;

/**
 * @author Adam Banaszkiewicz
 */
class FullPathStrategy extends AbstractRoutingStrategy
{
    public function __construct(
        TermPathReadStorageInterface $storage,
        TermFinderInterface $termFinder,
        ContentTypeRegistryInterface $contentTypeRegistry,
        LoggerInterface $logger,
        TagAwareCacheInterface $taxonomyCache,
    ) {
        parent::__construct($storage, $termFinder, $contentTypeRegistry, $logger, $taxonomyCache);
    }

    public function collectVisibleTermsGrouppedByTaxonomy(string $websiteId, string $locale): array
    {
        $terms = $this->storage->collectVisibleTerms($websiteId, $locale);
        $result = [];

        foreach ($terms as $key => $term) {
            if ($term['is_root']) {
                unset($terms[$key]);
                $result += $this->createTermsPathsRecursive($terms, $term['id']);
            }
        }

        return $result;
    }

    private function createTermsPathsRecursive(array $terms, string $parentId, string $parentPath = ''): array
    {
        foreach ($terms as $key => $term) {
            if ($term['parent_id'] === $parentId) {
                $terms[$key]['path'] = $parentPath.sprintf('/%s', $term['slug']);

                $terms = $this->createTermsPathsRecursive($terms, $terms[$key]['id'], $terms[$key]['path']);
            }
        }

        return $terms;
    }

    public function generate(string $id, array $parameters = []): string
    {
        dump($id);exit;
        $path = '';
        $term = $this->storage->findTermToPathGeneration($id, $parameters['_locale']);

        while ($term !== null) {
            $path = "/{$term['slug']}" . $path;

            if ($term['parent_id'] && $term['parent_id'] !== Term::ROOT_ID) {
                $term = $this->storage->findTermToPathGeneration($term['parent_id'], $parameters['_locale']);
            } else {
                break;
            }
        }

        return $path;
    }

    public function supports(string $contentType): bool
    {
        return $contentType === 'taxonomy';
    }

    public function getId(): string
    {
        return 'full-path';
    }
}
