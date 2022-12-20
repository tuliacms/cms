<?php

declare(strict_types=1);

namespace Tulia\Cms\Shared\Domain\WriteModel\Service\SlugGeneratorStrategy;

use Tulia\Cms\Shared\Domain\WriteModel\Service\SluggerInterface;

/**
 * @author Adam Banaszkiewicz
 */
final class DefaultSlugGeneratorStrategy implements SlugGeneratorStrategyInterface
{
    /**
     * @param iterable|SlugExistenceCheckerInterface[] $slugExistenceCheckers
     */
    public function __construct(
        private readonly iterable $slugExistenceCheckers,
        private readonly SluggerInterface $slugger,
    ) {
    }

    public function generate(
        ?string $slug,
        ?string $nameOrTitle,
    ): string {
        $slug = (string) $slug;
        $nameOrTitle = (string) $nameOrTitle;

        if (! trim($slug) && ! trim($nameOrTitle)) {
            return uniqid('temporary-slug-', true);
        }

        // Fallback to elements' nameOrTitle, if no slug provided.
        $input = $slug ?: $nameOrTitle;

        return (string) $this->slugger->url($input);
    }

    public function generateGloballyUnique(
        string $elementId,
        ?string $slug,
        ?string $nameOrTitle,
        string $websiteId,
        string $locale,
    ): string {
        $securityLoop  = 0;
        $slugGenerated = $this->generate($slug, $nameOrTitle);

        while ($securityLoop <= 100) {
            $slugProposed = $slugGenerated;

            if ($securityLoop > 0) {
                $slugProposed .= '-' . $securityLoop;
            }

            $securityLoop++;

            $isUnique = $this->isUnique($slugProposed, $websiteId, $locale, $elementId);

            if ($isUnique === true) {
                return $slugProposed;
            }
        }

        return $slugGenerated . '-' . time();
    }

    private function isUnique(string $slugProposed, string $websiteId, string $locale, string $elementId): bool
    {
        foreach ($this->slugExistenceCheckers as $checker) {
            $exists = $checker->exists($slugProposed, $elementId, $websiteId, $locale);

            if ($exists) {
                return false;
            }
        }

        return true;
    }
}
