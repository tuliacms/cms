<?php

declare(strict_types=1);

namespace Tulia\Cms\Taxonomy\UserInterface\Web\Frontend\Controller;

use Symfony\Component\HttpFoundation\Request;
use Tulia\Cms\Node\Domain\ReadModel\Finder\NodeFinderInterface;
use Tulia\Cms\Node\Domain\ReadModel\Finder\NodeFinderScopeEnum;
use Tulia\Cms\Options\Domain\ReadModel\Options;
use Tulia\Cms\Platform\Infrastructure\Framework\Controller\AbstractController;
use Tulia\Cms\Platform\Infrastructure\Framework\Routing\Website\WebsiteInterface;
use Tulia\Cms\Platform\Shared\Document\DocumentInterface;
use Tulia\Cms\Platform\Shared\Pagination\Paginator;
use Tulia\Cms\Seo\Domain\Service\SeoDocumentProcessorInterface;
use Tulia\Cms\Taxonomy\Domain\ReadModel\Model\Term as QueryModelTerm;
use Tulia\Component\Templating\ViewInterface;

/**
 * @author Adam Banaszkiewicz
 */
class Term extends AbstractController
{
    public function __construct(
        private readonly NodeFinderInterface $nodeFinder,
        private readonly Options $options,
    ) {
    }

    public function show(
        QueryModelTerm $term,
        Request $request,
        WebsiteInterface $website,
        SeoDocumentProcessorInterface $seo,
        DocumentInterface $document,
    ): ViewInterface {
        $page = max(1, $request->query->getInt('page', 1));
        $perPage = (int) $this->options->get(
            sprintf('taxonomy.%s.%s', $term->getType(), 'nodes_per_page'),
            9,
            $website->getId(),
            $website->getLocale()->getCode(),
        );

        $seo->aware(
            $term,
            $website->getId(),
            $website->getLocale()->getCode(),
            $term->getName(),
        );

        if ($page <= 1) {
            $document->addLink([
                'rel' => 'canonical',
                'href' => $request->getUriForPath($this->generateUrl(sprintf('frontend.taxonomy.%s.%s', $term->getType(), $term->getId())))
            ]);
        }

        $nodes = $this->nodeFinder->find([
            'term' => $term->getId(),
            'page' => $page,
            'per_page' => $perPage,
            'website_id' => $website->getId(),
            'locale' => $website->getLocale()->getCode(),
        ], NodeFinderScopeEnum::LISTING);

        return $this->view([
            '@cms/taxonomy/term_id:' . $term->getId() . '.tpl',
            '@cms/taxonomy/term_type:' . $term->getType() . '.tpl',
            '@cms/taxonomy/term.tpl',
        ], [
            'term' => $term,
            'nodes' => $nodes,
            'paginator' => new Paginator($request, $nodes->totalRows(), $page, $perPage)
        ]);
    }
}
