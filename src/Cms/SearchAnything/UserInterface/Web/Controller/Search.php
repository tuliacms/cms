<?php

declare(strict_types=1);

namespace Tulia\Cms\SearchAnything\UserInterface\Web\Controller;

use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Tulia\Cms\Platform\Infrastructure\Framework\Controller\AbstractController;
use Tulia\Cms\SearchAnything\Domain\ReadModel\Query\SearchEngineInterface;
use Tulia\Cms\Platform\Infrastructure\Framework\Routing\Website\WebsiteInterface;

/**
 * @author Adam Banaszkiewicz
 */
class Search extends AbstractController
{
    public function __construct(
        private SearchEngineInterface $searchEngine,
        private UrlGeneratorInterface $urlGenerator
    ) {
    }

    public function search(Request $request, WebsiteInterface $website): JsonResponse
    {
        $query = $request->query->get('q');

        $result = $this->searchEngine->search($query, $website->getLocale()->getCode(), 0, 10);

        foreach ($result as $key => $row) {
            $result[$key]['link'] = $this->urlGenerator->generate($row['route'], $row['route_parameters']);
            unset($result[$key]['route'], $result[$key]['route_parameters']);
        }

        return new JsonResponse($result);
    }
}
