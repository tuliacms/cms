<?php

declare(strict_types=1);

namespace Tulia\Cms\Website\Infrastructure\Framework\Profiler;

use Symfony\Bundle\FrameworkBundle\DataCollector\AbstractDataCollector;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Tulia\Cms\Platform\Infrastructure\Framework\Routing\Website\WebsiteInterface;

/**
 * @author Adam Banaszkiewicz
 */
final class WebsiteCollector extends AbstractDataCollector
{
    public function __construct(
        private readonly WebsiteInterface $website,
    ) {
    }

    public function collect(Request $request, Response $response, \Throwable $exception = null)
    {
        $this->data = [
            'id' => $this->website->getId(),
            'name' => $this->website->getName(),
            'active' => $this->website->isEnabled(),
            'backendPrefix' => $this->website->getBackendPrefix(),
            'isBackend' => $this->website->isBackend(),
            'locale' => [
                'code' => $this->website->getLocale()->getCode(),
                'domain' => $this->website->getLocale()->getDomain(),
                'localePrefix' => $this->website->getLocale()->getLocalePrefix(),
                'pathPrefix' => $this->website->getLocale()->getPathPrefix(),
                'sslMode' => $this->website->getLocale()->getSslMode(),
            ],
            'defaultLocale' => [
                'code' => $this->website->getDefaultLocale()->getCode(),
                'domain' => $this->website->getDefaultLocale()->getDomain(),
                'localePrefix' => $this->website->getDefaultLocale()->getLocalePrefix(),
                'pathPrefix' => $this->website->getDefaultLocale()->getPathPrefix(),
                'sslMode' => $this->website->getDefaultLocale()->getSslMode(),
            ],
        ];
    }

    public static function getTemplate(): ?string
    {
        return '@website/profiler/website_collector.html.twig';
    }

    public function isWebsiteSet(): bool
    {
        return isset($this->data['id']);
    }

    public function getId(): string
    {
        return $this->data['id'];
    }

    public function isActive(): bool
    {
        return $this->data['active'];
    }

    public function getWebsiteName(): string
    {
        return $this->data['name'];
    }

    public function getBackendPrefix(): string
    {
        return $this->data['backendPrefix'];
    }

    public function getLocale(): array
    {
        return $this->data['locale'];
    }

    public function getDefaultLocale(): array
    {
        return $this->data['defaultLocale'];
    }
}
