<?php

declare(strict_types=1);

namespace Tulia\Cms\BodyClass\Collector;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Tulia\Component\Theme\Customizer\DetectorInterface;

/**
 * @author Adam Banaszkiewicz
 */
class DefaultBodyClassCollector implements BodyClassCollectorInterface
{
    public function __construct(
        private readonly DetectorInterface $detector,
        private readonly UrlGeneratorInterface $urlGenerator,
    ) {
    }

    public function collect(Request $request, BodyClassCollection $collection): void
    {
        if ($request->getPathInfo() === $this->urlGenerator->generate('frontend.homepage')) {
            $collection->add('is-homepage');
        }

        if ($this->detector->isCustomizerMode()) {
            $collection->add('is-customizer');
        }

        $collection->add('locale-' . $request->attributes->get('_locale'));
    }
}
