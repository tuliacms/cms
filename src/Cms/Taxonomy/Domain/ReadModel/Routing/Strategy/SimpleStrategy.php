<?php

declare(strict_types=1);

namespace Tulia\Cms\Taxonomy\Domain\ReadModel\Routing\Strategy;

use Symfony\Component\Routing\Exception\RouteNotFoundException;

/**
 * @author Adam Banaszkiewicz
 */
class SimpleStrategy extends AbstractRoutingStrategy
{
    public function generate(string $id, array $parameters = []): string
    {
        $terms = $this->collectVisibleTermsGrouppedByTaxonomy($parameters['_website'], $parameters['_locale']);

        foreach ($terms as $term) {
            if ($term['id'] === $id) {
                return $term['path'];
            }
        }

        throw new RouteNotFoundException('Cannot find term for this identity.');
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
