<?php

declare(strict_types=1);

namespace Tulia\Cms\Platform\Infrastructure\Framework\Twig\Extension;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

/**
 * @author Adam Banaszkiewicz
 */
class RequestExtension extends AbstractExtension
{
    public function __construct(
        private readonly RequestStack $requestStack,
        private readonly UrlGeneratorInterface $urlGenerator,
    ) {
    }

    public function getFunctions(): array
    {
        return [
            new TwigFunction('is_homepage', function () {
                // @todo Move checking homepage to separate class with cache.
                // The same code sits in \Tulia\Cms\Platform\Infrastructure\Framework\Controller\AbstractController::isHomepage
                // The same code sits in src/Cms/BodyClass/Collector/DefaultBodyClassCollector.php:24
                return $this->getRequest()->getPathInfo() === $this->urlGenerator->generate('frontend.homepage');
            }, [
                'is_safe' => [ 'html' ]
            ]),
            new TwigFunction('get_flashes', function (array $types = []) {
                return $this->getFlashes($types);
            }, [
                'is_safe' => [ 'html' ]
            ]),
            new TwigFunction('flashes', function (array $types = [], array $options = []) {
                $options = array_merge($options, [
                    'html-class-replace' => [],
                    'html-class-base'    => 'alert',
                    'html-class-prefix'  => 'alert-',
                    'dismissable'        => true
                ]);

                $result = '';

                foreach ($this->getFlashes($types) as $type => $messages) {
                    foreach ($messages as $message) {
                        $classname = $options['html-class-replace'][$type] ?? $type;

                        $htmlClass = [];

                        if ($options['html-class-base']) {
                            $htmlClass[] = $options['html-class-base'];
                        }

                        $htmlClass[] = $options['html-class-prefix'] . $classname;

                        if ($options['dismissable']) {
                            $htmlClass[] = 'alert-dismissible fade show';
                        }

                        $result .= '<div class="' . implode(' ', $htmlClass) . '">' . $message . ($options['dismissable'] ? '<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>' : '' ) . '</div>';
                    }
                }

                return $result;
            }, [
                'is_safe' => [ 'html' ]
            ]),
        ];
    }

    protected function getFlashes(array $types = []): array
    {
        if (false === $this->getRequest()->hasSession()) {
            return [];
        }

        $types = $types === [] ? [ 'info', 'success', 'warning', 'danger' ] : $types;
        $flashbag = $this->getRequest()->getSession()->getFlashBag();
        $flashes  = [];

        foreach ($types as $type) {
            $flashes[$type] = $flashbag->get($type);
        }

        return $flashes;
    }

    protected function getRequest(): Request
    {
        /** @var Request $request */
        $request = $this->requestStack->getMainRequest();

        return $request;
    }
}
