<?php

declare(strict_types=1);

namespace Tulia\Cms\Menu\Infrastructure\Framework\Twig\Extension;

use Tulia\Cms\Menu\Domain\Builder\BuilderInterface;
use Tulia\Cms\Menu\Domain\Builder\Criteria;
use Tulia\Cms\Menu\Domain\ReadModel\Finder\MenuFinderInterface;
use Tulia\Cms\Platform\Infrastructure\Framework\Routing\Website\WebsiteInterface;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

/**
 * @author Adam Banaszkiewicz
 */
class MenuExtension extends AbstractExtension
{
    public function __construct(
        private readonly BuilderInterface $builder,
        private readonly WebsiteInterface $website,
        private readonly MenuFinderInterface $menuFinder,
    ) {
    }

    public function getFunctions(): array
    {
        return [
            new TwigFunction('menu_space', function (string $space, string $layout) {
                return $this->builder->buildHtml(
                    Criteria::bySpace(
                        $space,
                        $this->website->getId(),
                        $this->website->getLocale()->getCode(),
                    ),
                    $layout,
                );
            }, [
                'is_safe' => [ 'html' ]
            ]),
        ];
    }
}
