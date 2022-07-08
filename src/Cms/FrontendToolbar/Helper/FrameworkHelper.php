<?php

declare(strict_types=1);

namespace Tulia\Cms\FrontendToolbar\Helper;

use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Contracts\Translation\TranslatorInterface;
use Tulia\Component\Templating\EngineInterface;
use Tulia\Component\Templating\ViewInterface;

/**
 * @author Adam Banaszkiewicz
 */
class FrameworkHelper implements HelperInterface
{
    public function __construct(
        protected TranslatorInterface $translator,
        protected RouterInterface $router,
        protected EngineInterface $engine,
        protected RequestStack $requestStack
    ) {
    }

    public function generateUrl(string $route, array $parameters = [], int $referenceType = RouterInterface::ABSOLUTE_PATH): string
    {
        return $this->router->generate($route, $parameters, $referenceType);
    }

    public function trans($id, array $parameters = [], $domain = null, $locale = null): string
    {
        return $this->translator->trans($id, $parameters, $domain, $locale);
    }

    public function render(ViewInterface $view): string
    {
        return $this->engine->render($view);
    }
}
