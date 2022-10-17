<?php

declare(strict_types=1);

namespace Tulia\Cms\Node\UserInterface\Web\Backend\Controller;

use Tulia\Cms\Node\Domain\ReadModel\Finder\NodeFinderScopeEnum;
use Tulia\Cms\Node\Domain\ReadModel\Finder\NodeFinderInterface;
use Tulia\Cms\Platform\Infrastructure\Framework\Controller\TypeaheadFormTypeSearch;
use Symfony\Component\HttpFoundation\Request;
use Tulia\Cms\Platform\Infrastructure\Framework\Routing\Website\WebsiteInterface;

/**
 * @author Adam Banaszkiewicz
 */
class TypeaheadSearch extends TypeaheadFormTypeSearch
{
    public function __construct(
        private NodeFinderInterface $nodeFinder
    ) {
    }

    protected function findCollection(Request $request, WebsiteInterface $website): array
    {
        $nodes = $this->nodeFinder->find([
            'search' => $request->query->get('q'),
            'node_type' => $request->query->get('node_type'),
            'per_page' => 10,
            'locale' => $website->getLocale()->getCode(),
            'website_id' => $website->getId(),
        ], NodeFinderScopeEnum::INTERNAL);

        $result = [];

        foreach ($nodes as $node) {
            $result[] = [
                'id' => $node->getId(),
                'title' => $node->getTitle(),
            ];
        }

        return $result;
    }
}
