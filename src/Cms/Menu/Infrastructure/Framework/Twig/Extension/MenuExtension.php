<?php

declare(strict_types=1);

namespace Tulia\Cms\Menu\Infrastructure\Framework\Twig\Extension;

use Tulia\Cms\Menu\Domain\Builder\BuilderInterface;
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
    ) {
    }

    /**
     * {@inheritdoc}
     */
    public function getFunctions(): array
    {
        return [
            new TwigFunction('show_menu', function (string $id) {
                return $this->builder->buildHtml(
                    $id,
                    $this->website->getId(),
                    $this->website->getLocale()->getCode(),
                );
            }, [
                'is_safe' => [ 'html' ]
            ]),
        ];
    }
}
