<?php

declare(strict_types=1);

namespace Tulia\Cms\Widget\Infrastructure\Framework\Twig\Extension;

use Tulia\Cms\Widget\Domain\Catalog\Storage\StorageInterface;
use Tulia\Cms\Widget\Domain\Renderer\RendererInterface;
use Tulia\Component\Routing\Website\WebsiteInterface;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

/**
 * @author Adam Banaszkiewicz
 */
class WidgetExtension extends AbstractExtension
{
    public function __construct(
        private RendererInterface $renderer,
        private StorageInterface $storage,
        private WebsiteInterface $website
    ) {
    }

    /**
     * {@inheritdoc}
     */
    public function getFunctions(): array
    {
        return [
            new TwigFunction('widget', function (string $id) {
                return $this->renderer->forId($id, $this->website->getLocale()->getCode());
            }, [
                'is_safe' => [ 'html' ]
            ]),
            new TwigFunction('widgets_space', function (string $space) {
                return $this->renderer->forSpace($space, $this->website->getLocale()->getCode());
            }, [
                'is_safe' => [ 'html' ]
            ]),
            new TwigFunction('widgets_space_count', function (string $space) {
                return count($this->storage->findBySpace($space, $this->website->getLocale()->getCode()));
            }, [
                'is_safe' => [ 'html' ]
            ]),
        ];
    }
}
