<?php

declare(strict_types=1);

namespace Tulia\Cms\FrontendToolbar\Builder;

use Symfony\Component\HttpFoundation\Request;
use Tulia\Cms\FrontendToolbar\Links\LinksCollection;
use Tulia\Cms\FrontendToolbar\Links\ProviderRegistry;
use Tulia\Component\Templating\EngineInterface;
use Tulia\Component\Templating\View;

/**
 * @author Adam Banaszkiewicz
 */
class Builder
{
    public function __construct(
        private readonly ProviderRegistry $registry,
        private readonly EngineInterface $engine,
    ) {
    }

    public function build(Request $request): string
    {
        $links = new LinksCollection();
        $contents = '';

        foreach ($this->registry->all() as $provider) {
            $provider->collect($links, $request);

            $contents .= $provider->provideContent($request);
        }

        $linksCollection = $links->all();

        foreach ($linksCollection as $link) {
            $attrs = $link->getAttributes();
            $attrs['class'] = ($attrs['class'] ?? '').' tulia-toolbar-item';

            $link->setAttributes($attrs);
        }

        return $this->engine->render(new View('@cms/frontend_toolbar/toolbar.tpl', [
            'links' => $linksCollection,
            'contents' => $contents,
        ]));
    }
}
