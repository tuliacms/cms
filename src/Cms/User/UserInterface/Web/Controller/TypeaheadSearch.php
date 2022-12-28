<?php

declare(strict_types=1);

namespace Tulia\Cms\User\UserInterface\Web\Controller;

use Symfony\Component\HttpFoundation\Request;
use Tulia\Cms\Platform\Infrastructure\Framework\Controller\TypeaheadFormTypeSearch;
use Tulia\Cms\User\Domain\ReadModel\Finder\UserFinderInterface;
use Tulia\Cms\User\Domain\ReadModel\Finder\UserFinderScopeEnum;
use Tulia\Cms\Platform\Infrastructure\Framework\Routing\Website\WebsiteInterface;

/**
 * @author Adam Banaszkiewicz
 */
class TypeaheadSearch extends TypeaheadFormTypeSearch
{
    public function __construct(
        private readonly UserFinderInterface $userFinder,
    ) {
    }

    protected function findCollection(Request $request, WebsiteInterface $website): array
    {
        $users = $this->userFinder->find([
            'search'   => $request->query->get('q'),
            'per_page' => 10,
        ], UserFinderScopeEnum::INTERNAL);

        $result = [];

        foreach ($users as $row) {
            $username = $row->getEmail();

            if ($row->getName()) {
                $username = $row->getName() . " ({$username})";
            }

            $result[] = ['username' => $username];
        }

        return $result;
    }
}
