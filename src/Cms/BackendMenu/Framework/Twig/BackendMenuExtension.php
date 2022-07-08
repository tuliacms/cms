<?php

declare(strict_types=1);

namespace Tulia\Cms\BackendMenu\Framework\Twig;

use Symfony\Component\HttpFoundation\RequestStack;
use Tulia\Cms\BackendMenu\Builder\HtmlBuilderInterface;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

/**
 * @author Adam Banaszkiewicz
 */
class BackendMenuExtension extends AbstractExtension
{
    public function __construct(
        private HtmlBuilderInterface $builder
    ) {
    }

    public function getFunctions(): array
    {
        return [
            new TwigFunction('backend_menu', function ($context) {
                $cookie = $context['app']->getRequest()->cookies->get('aside-menuitems-opened');
                $ids = $cookie ? explode('|', $cookie) : [];

                return $this->builder->build([
                    'opened' => $ids,
                ]);
            }, [
                'is_safe' => ['html'],
                'needs_context' => true,
            ])
        ];
    }
}
