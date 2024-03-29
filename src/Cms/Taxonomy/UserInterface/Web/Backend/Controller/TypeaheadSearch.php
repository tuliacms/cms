<?php

declare(strict_types=1);

namespace Tulia\Cms\Taxonomy\UserInterface\Web\Backend\Controller;

use Symfony\Component\HttpFoundation\Request;
use Tulia\Cms\Platform\Infrastructure\Framework\Controller\TypeaheadFormTypeSearch;
use Tulia\Cms\Taxonomy\Domain\ReadModel\Finder\TermFinderInterface;
use Tulia\Cms\Taxonomy\Domain\ReadModel\Finder\TermFinderScopeEnum;
use Tulia\Cms\Platform\Infrastructure\Framework\Routing\Website\WebsiteInterface;

/**
 * @author Adam Banaszkiewicz
 */
class TypeaheadSearch extends TypeaheadFormTypeSearch
{
    public function __construct(
        private readonly TermFinderInterface $termFinder,
    ) {
    }

    protected function findCollection(Request $request, WebsiteInterface $website): array
    {
        $terms = $this->termFinder->find([
            'search'        => $request->query->get('q'),
            'taxonomy_type' => $request->query->get('taxonomy_type'),
            'per_page'      => 10,
            'locale'        => $website->getLocale()->getCode(),
            'website_id'    => $website->getId(),
        ], TermFinderScopeEnum::INTERNAL);

        $result = [];

        foreach ($terms as $term) {
            $result[] = [
                'id' => $term->getId(),
                'name' => $term->getName(),
            ];
        }

        return $result;
    }
}
