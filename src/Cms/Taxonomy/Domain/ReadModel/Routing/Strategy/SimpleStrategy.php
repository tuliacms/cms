<?php

declare(strict_types=1);

namespace Tulia\Cms\Taxonomy\Domain\ReadModel\Routing\Strategy;

use Psr\Log\LoggerInterface;
use Symfony\Contracts\Cache\TagAwareCacheInterface;
use Tulia\Cms\Content\Type\Domain\ReadModel\Service\ContentTypeRegistryInterface;
use Tulia\Cms\Taxonomy\Domain\ReadModel\Finder\TermFinderInterface;
use Tulia\Cms\Taxonomy\Domain\ReadModel\Service\TermPathReadStorageInterface;

/**
 * @author Adam Banaszkiewicz
 */
class SimpleStrategy extends AbstractRoutingStrategy
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
        return [];
    }

    public function generate(string $id, array $parameters = []): string
    {
        return '/' . $parameters['_term_instance']->getSlug();
    }

    public function supports(string $contentType): bool
    {
        return $contentType === 'taxonomy';
    }

    public function getId(): string
    {
        return 'simple';
    }
}
