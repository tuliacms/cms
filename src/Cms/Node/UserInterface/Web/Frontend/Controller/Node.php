<?php

declare(strict_types=1);

namespace Tulia\Cms\Node\UserInterface\Web\Frontend\Controller;

use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Tulia\Cms\Node\Domain\ReadModel\Model\Node as Model;
use Tulia\Cms\Node\Domain\WriteModel\Model\Enum\NodePurposeEnum;
use Tulia\Cms\Platform\Infrastructure\Framework\Controller\AbstractController;
use Tulia\Cms\Platform\Infrastructure\Framework\Routing\Website\WebsiteInterface;
use Tulia\Cms\Seo\Domain\Service\SeoDocumentProcessorInterface;
use Tulia\Cms\Taxonomy\Domain\ReadModel\Finder\TermFinderInterface;
use Tulia\Cms\Taxonomy\Domain\ReadModel\Finder\TermFinderScopeEnum;
use Tulia\Cms\Taxonomy\Domain\ReadModel\Model\Term;
use Tulia\Component\Templating\ViewInterface;

/**
 * @author Adam Banaszkiewicz
 */
class Node extends AbstractController
{
    public function __construct(
        private readonly TermFinderInterface $termFinder,
    ) {
    }

    /**
     * @return RedirectResponse|ViewInterface
     */
    public function show(
        Model $node,
        Request $request,
        SeoDocumentProcessorInterface $seo,
        WebsiteInterface $website,
    ) {
        if (
            $node->hasPurpose(NodePurposeEnum::PAGE_HOMEPAGE)
            && $this->isHomepage($request) === false
        ) {
            return $this->redirectToRoute('frontend.homepage');
        }

        $seo->aware(
            $node,
            $website->getId(),
            $website->getLocale()->getCode(),
            $node->getTitle(),
        );

        $category = $this->findTerm($node);

        return $this->view($this->createViews($node, $category), [
            'node'     => $node,
            'category' => $category,
        ]);
    }

    private function findTerm(Model $node): ?Term
    {
        if ($node->getCategory()) {
            return $this->termFinder->findOne([
                'id' => $node->getCategory(),
                'locale' => $node->getLocale(),
                'website_id' => $node->getWebsiteId(),
            ], TermFinderScopeEnum::SINGLE);
        }

        return null;
    }

    private function createViews(Model $node, ?Term $category): array
    {
        $views = [];
        $views[] = '@cms/node/node_id:' . $node->getId() . '.tpl';
        $views[] = '@cms/node/node_type:' . $node->getType() . '.tpl';

        if ($category) {
            $views[] = '@cms/node/node_type:' . $node->getType() . '_taxonomy:' . $category->getType() . '.tpl';
            $views[] = '@cms/node/node_taxonomy:' . $category->getType() . '.tpl';
        }

        $views[] = '@cms/node/node.tpl';

        return $views;
    }
}
