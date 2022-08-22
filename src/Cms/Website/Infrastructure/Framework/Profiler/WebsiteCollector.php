<?php

declare(strict_types=1);

namespace Tulia\Cms\Website\Infrastructure\Framework\Profiler;

use Symfony\Bundle\FrameworkBundle\DataCollector\AbstractDataCollector;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Tulia\Component\Routing\Website\Website;

/**
 * @author Adam Banaszkiewicz
 */
final class WebsiteCollector extends AbstractDataCollector
{
    public function collect(Request $request, Response $response, \Throwable $exception = null)
    {
        /** @var Website $website */
        $website = $request->attributes->get('website');

        if (!$website) {
            return;
        }

        $this->data = [
            'id' => $website->getId(),
            'name' => $website->getName(),
            'active' => $website->isActive(),
            'backendPrefix' => $website->getBackendPrefix(),
            'isBackend' => $website->isBackend(),
            'basepath' => $website->getBasepath(),
            'locale' => [
                'code' => $website->getLocale()->getCode(),
                'domain' => $website->getLocale()->getDomain(),
                'localePrefix' => $website->getLocale()->getLocalePrefix(),
                'pathPrefix' => $website->getLocale()->getPathPrefix(),
                'sslMode' => $website->getLocale()->getSslMode(),
            ],
            'defaultLocale' => [
                'code' => $website->getDefaultLocale()->getCode(),
                'domain' => $website->getDefaultLocale()->getDomain(),
                'localePrefix' => $website->getDefaultLocale()->getLocalePrefix(),
                'pathPrefix' => $website->getDefaultLocale()->getPathPrefix(),
                'sslMode' => $website->getDefaultLocale()->getSslMode(),
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
