<?php

declare(strict_types=1);

namespace Tulia\Cms\Homepage\UserInterface\Web\Frontend\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Tulia\Cms\Node\Domain\ReadModel\Finder\NodeFinderInterface;
use Tulia\Cms\Node\Domain\ReadModel\Finder\NodeFinderScopeEnum;
use Tulia\Cms\Node\Domain\WriteModel\Model\Enum\NodePurposeEnum;
use Tulia\Cms\Node\UserInterface\Web\Frontend\Controller\Node;
use Tulia\Cms\Platform\Infrastructure\Framework\Controller\AbstractController;
use Tulia\Component\Routing\Website\WebsiteInterface;
use Tulia\Component\Templating\ViewInterface;

/**
 * @author Adam Banaszkiewicz
 */
class Homepage extends AbstractController
{
    public function __construct(
        private readonly NodeFinderInterface $nodeFinder,
        private readonly Node $nodeController,
    ) {
    }

    /**
     * @return Response|ViewInterface
     */
    public function index(Request $request, WebsiteInterface $website)
    {
        $homepage = $this->nodeFinder->findOne([
            'purpose' => NodePurposeEnum::PAGE_HOMEPAGE,
            'locale' => $website->getLocale()->getCode(),
            'website_id' => $website->getId(),
        ], NodeFinderScopeEnum::SINGLE);

        if ($homepage) {
            return $this->nodeController->show($homepage, $request);
        }

        return $this->view('@cms/homepage/index.tpl');
    }
}
