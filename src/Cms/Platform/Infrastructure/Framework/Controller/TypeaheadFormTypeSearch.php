<?php

declare(strict_types=1);

namespace Tulia\Cms\Platform\Infrastructure\Framework\Controller;

use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Tulia\Cms\Platform\Infrastructure\Framework\Routing\Website\WebsiteInterface;

/**
 * @author Adam Banaszkiewicz
 */
abstract class TypeaheadFormTypeSearch extends AbstractController
{
    abstract protected function findCollection(Request $request, WebsiteInterface $website): array;

    public function handleSearch(Request $request, WebsiteInterface $website): JsonResponse
    {
        return $this->responseJson([
            'status' => true,
            'result' => $this->findCollection($request, $website),
        ]);
    }
}
