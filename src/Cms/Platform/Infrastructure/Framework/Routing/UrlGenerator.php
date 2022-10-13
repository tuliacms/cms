<?php

declare(strict_types=1);

namespace Tulia\Cms\Platform\Infrastructure\Framework\Routing;

use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Routing\RequestContext;
use Tulia\Cms\Platform\Infrastructure\Framework\Routing\Website\WebsiteInterface;

/**
 * @author Adam Banaszkiewicz
 */
final class UrlGenerator implements UrlGeneratorInterface
{
    public function __construct(
        private readonly UrlGeneratorInterface $symfonyUrlGenerator,
        private readonly WebsiteInterface $website,
    ) {
    }

    public function setContext(RequestContext $context)
    {
        $this->symfonyUrlGenerator->setContext($context);
    }

    public function getContext(): RequestContext
    {
        return $this->symfonyUrlGenerator->getContext();
    }

    public function generate(string $name, array $parameters = [], int $referenceType = self::ABSOLUTE_PATH): string
    {
        if (0 === strncmp($name, 'backend.', 8) || 0 === strncmp($name, 'frontend.', 9)) {
            $name = sprintf(
                '%s.%s.%s',
                $parameters['_website'] ?? $this->website->getId(),
                $parameters['_locale'] ?? $this->website->getLocale()->getCode(),
                $name
            );
        }

        return $this->symfonyUrlGenerator->generate($name, $parameters, $referenceType);
    }
}
