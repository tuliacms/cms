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
        private SluggerInterface $slugger,
        private NodeSlugUniquenessInterface $nodeSlugUniqueness
    ) {
    }

    public function generate(
        string $website,
        string $locale,
        string $slug,
        string $title,
        string $nodeId
    ): string {
        if (! trim($slug) && ! trim($title)) {
            return uniqid('temporary-slug-', true);
        }

        // Fallback to Node's title, if no slug provided.
        $input = $slug ?: $title;

        return $this->findUniqueSlug($website, $locale, $input, $nodeId);
    }

    private function findUniqueSlug(
        string $website,
        string $locale,
        string $slug,
        ?string $nodeId
    ): string {
        $securityLoop  = 0;
        $slugGenerated = $this->slugger->url($slug);

        while ($securityLoop <= 100) {
            $slugProposed = $slugGenerated;

            if ($securityLoop > 0) {
                $slugProposed .= '-' . $securityLoop;
            }

            $securityLoop++;

            $isUnique = $this->nodeSlugUniqueness->isUnique($website, $locale, $slugProposed, $nodeId);

            if ($isUnique === true) {
                return $slugProposed;
            }
        }

        return $slug . '-' . time();
    }
}
