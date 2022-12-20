<?php

declare(strict_types=1);

namespace Tulia\Cms\Node\Domain\WriteModel\Service\SlugGeneratorStrategy;

use Tulia\Cms\Node\Domain\WriteModel\Service\NodeSlugUniquenessInterface;
use Tulia\Cms\Shared\Domain\WriteModel\Service\SluggerInterface;

/**
 * @author Adam Banaszkiewicz
 */
final class DefaultSlugGeneratorStrategy implements SlugGeneratorStrategyInterface
{
    public function __construct(
        private readonly SluggerInterface $slugger,
        private readonly NodeSlugUniquenessInterface $nodeSlugUniqueness,
    ) {
    }

    public function generate(
        string $nodeId,
        string $slug,
        string $title,
        string $websiteId,
        ?string $locale = null,
    ): string {
        if (! trim($slug) && ! trim($title)) {
            return uniqid('temporary-slug-', true);
        }

        // Fallback to Node's title, if no slug provided.
        $input = $slug ?: $title;

        return $this->findUniqueSlug($nodeId, $input, $websiteId, $locale);
    }

    private function findUniqueSlug(
        ?string $nodeId,
        string $slug,
        string $websiteId,
        ?string $locale,
    ): string {
        $securityLoop  = 0;
        $slugGenerated = (string) $this->slugger->url($slug);

        while ($securityLoop <= 100) {
            $slugProposed = $slugGenerated;

            if ($securityLoop > 0) {
                $slugProposed .= '-' . $securityLoop;
            }

            $securityLoop++;

            $isUnique = $this->nodeSlugUniqueness->isUnique($slugProposed, $websiteId, $locale, $nodeId);

            if ($isUnique === true) {
                return $slugProposed;
            }
        }

        return $slug . '-' . time();
    }
}
