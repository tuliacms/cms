<?php

declare(strict_types=1);

namespace Tulia\Cms\Breadcrumbs\Framework\Twig\Extension;

use Tulia\Cms\Breadcrumbs\Domain\BreadcrumbsGeneratorInterface;
use Tulia\Cms\Platform\Shared\Document\DocumentInterface;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

/**
 * @author Adam Banaszkiewicz
 */
class BreadcrumbsExtension extends AbstractExtension
{
    public function __construct(
        private readonly BreadcrumbsGeneratorInterface $generator,
        private readonly DocumentInterface $document,
    ) {
    }

    public function getFunctions(): array
    {
        return [
            new TwigFunction('breadcrumbs', function ($context, ?string $classlist = null) {
                $breadcrumbs = $this->generator->generateFromRequest($context['app']->getRequest());

                // Append current page placeholder if at least
                // homepage crumb is in collection.
                /*if ($breadcrumbs->count() <= 1) {
                    $breadcrumbs->push('#', $this->document->getTitle());
                }*/

                $breadcrumbs->setClasslist($classlist);

                return $breadcrumbs;
            }, [
                'needs_context' => true,
                'is_safe' => [ 'html' ],
            ]),
        ];
    }
}
