<?php

declare(strict_types=1);

namespace Tulia\Cms\Taxonomy\Infrastructure\Framework\Twig\Extension;

use Symfony\Component\Routing\RouterInterface;
use Tulia\Cms\Platform\Infrastructure\Framework\Routing\Website\WebsiteInterface;
use Tulia\Cms\Taxonomy\Domain\ReadModel\Finder\TermFinderInterface;
use Tulia\Cms\Taxonomy\Domain\ReadModel\Finder\TermFinderScopeEnum;
use Tulia\Cms\Taxonomy\Domain\ReadModel\Model\Term;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

/**
 * @author Adam Banaszkiewicz
 */
class TaxonomyExtension extends AbstractExtension
{
    public function __construct(
        private readonly RouterInterface $router,
        private readonly TermFinderInterface $termFinder,
        private readonly WebsiteInterface $website,
    ) {
    }

    /**
     * {@inheritdoc}
     */
    public function getFunctions(): array
    {
        return [
            new TwigFunction('term_path', function (Term $identity, array $parameters = []) {
                return $this->generate($identity, $parameters, RouterInterface::ABSOLUTE_PATH);
            }, [
                'is_safe' => [ 'html' ]
            ]),
            new TwigFunction('term_url', function (Term $identity, array $parameters = []) {
                return $this->generate($identity, $parameters, RouterInterface::ABSOLUTE_URL);
            }, [
                'is_safe' => [ 'html' ]
            ]),
            new TwigFunction('find_terms', function (array $criteria) {
                if (!isset($parameters['locale'])) {
                    $parameters['locale'] = $this->website->getLocale()->getCode();
                }
                if (!isset($parameters['website_id'])) {
                    $parameters['website_id'] = $this->website->getLocale()->getCode();
                }

                return $this->termFinder->find($criteria, TermFinderScopeEnum::TAXONOMY_LISTING);
            }, [
                'is_safe' => [ 'html' ]
            ]),
        ];
    }

    private function generate(Term $identity, array $parameters, int $type): string
    {
        return $this->router->generate($this->getId($identity), $parameters, $type);
    }

    private function getId(Term $identity): string
    {
        return sprintf('frontent.term.%s.%s', $identity->getType(), $identity->getId());
    }
}
