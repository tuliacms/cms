<?php

declare(strict_types=1);

namespace Tulia\Component\Datatable;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Contracts\Translation\TranslatorInterface;
use Tulia\Component\Datatable\Finder\FinderInterface;
use Tulia\Component\Datatable\Plugin\PluginsRegistry;
use Tulia\Component\Templating\EngineInterface;

/**
 * @author Adam Banaszkiewicz
 */
class DatatableFactory
{
    public function __construct(
        private readonly TranslatorInterface $translator,
        private readonly PluginsRegistry $pluginsRegistry,
        private readonly EngineInterface $engine,
    ) {
    }

    public function create(FinderInterface $finder, Request $request): Datatable
    {
        return new Datatable(
            $finder,
            $request,
            $this->translator,
            $this->engine,
            $this->pluginsRegistry->getForFinder($finder)
        );
    }
}
