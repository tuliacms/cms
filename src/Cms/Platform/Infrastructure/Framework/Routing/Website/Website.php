<?php

declare(strict_types=1);

namespace Tulia\Cms\Platform\Infrastructure\Framework\Routing\Website;

use Tulia\Cms\Platform\Infrastructure\Framework\Routing\Exception\LocaleNotExistsException;

/**
 * @author Adam Banaszkiewicz
 */
class Website implements WebsiteInterface
{
    private LocaleInterface $defaultLocale;
    private LocaleInterface $activeLocale;
    /** @var LocaleInterface[] */
    private array $locales = [];

    /** @param LocaleInterface[] $locales */
    public function __construct(
        private string $id,
        private string $name,
        private string $backendPrefix,
        private bool $isBackend,
        array $locales,
        string $defaultLocale,
        string $activeLocale,
        private bool $enabled,
    ) {
        foreach ($locales as $locale) {
            $this->locales[$locale->getCode()] = $locale;

            if ($locale->getCode() === $defaultLocale) {
                $this->defaultLocale = $locale;
            }
            if ($locale->getCode() === $activeLocale) {
                $this->activeLocale = $locale;
            }
        }

        if (!$this->defaultLocale) {
            throw new LocaleNotExistsException('Default locale not exists for this website.');
        }
        if (!$this->activeLocale) {
            throw new LocaleNotExistsException('Active locale not exists for this website.');
        }
    }

    public function getId(): string
    {
        return $this->id;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getBackendPrefix(): string
    {
        return $this->backendPrefix;
    }

    public function getLocales(): array
    {
        return $this->locales;
    }

    public function getLocale(): LocaleInterface
    {
        return $this->activeLocale;
    }

    public function getDefaultLocale(): LocaleInterface
    {
        return $this->defaultLocale;
    }

    public function isBackend(): bool
    {
        return $this->isBackend;
    }

    public function getLocaleByCode(string $code): LocaleInterface
    {
        foreach ($this->locales as $locale) {
            if ($locale->getCode() === $code) {
                return $locale;
            }
        }

        throw new LocaleNotExistsException(sprintf('Locale "%s" not exists in this website.', $code));
    }

    public function getAddress(?string $localeCode = null): string
    {
        $locale = $this->resolveLocale($localeCode);

        if ($locale->getSslMode() === SslModeEnum::FORCE_SSL) {
            return 'https://' . $locale->getDomain() . $locale->getPathPrefix() . $locale->getLocalePrefix() . '/';
        }

        return 'http://' . $locale->getDomain() . $locale->getPathPrefix() . $locale->getLocalePrefix() . '/';
    }

    public function getBackendAddress(?string $localeCode = null): string
    {
        $locale = $this->resolveLocale($localeCode);

        if ($locale->getSslMode() === SslModeEnum::FORCE_SSL) {
            return 'https://' . $locale->getDomain() . $locale->getPathPrefix() . $this->backendPrefix . $locale->getLocalePrefix() . '/';
        }

        return 'http://' . $locale->getDomain() . $locale->getPathPrefix() . $this->backendPrefix . $locale->getLocalePrefix() . '/';
    }

    public function isDefaultLocale(): bool
    {
        return $this->activeLocale->getCode() === $this->defaultLocale->getCode();
    }

    public function isEnabled(): bool
    {
        return $this->enabled;
    }

    public function getLocaleCodes(): array
    {
        return array_map(
            static fn(Locale $v) => $v->getCode(),
            $this->locales
        );
    }

    public function prepareRequestUriToRoutingMatching(string $requestUri): string
    {
        $locale = $this->getLocale();

        if ($locale->getPathPrefix() && strpos($requestUri, $locale->getPathPrefix()) === 0) {
            $requestUri = substr($requestUri, \strlen($locale->getPathPrefix()));
        }
        if ($this->isBackend()) {
            $requestUri = substr($requestUri, \strlen($this->getBackendPrefix()));
        }
        if ($locale->getLocalePrefix() && strpos($requestUri, $locale->getLocalePrefix()) === 0) {
            $requestUri = substr($requestUri, \strlen($locale->getLocalePrefix()));
        }

        if ($this->isBackend()) {
            $requestUri = '/administrator'.$requestUri;
        }

        return $requestUri;
    }

    public function generateTargetPath(
        string $path,
        string $locale,
        ?bool $backend = false,
    ): string {
        $activeLocale = $this->getLocaleByCode($locale);
        $localePrefix = $activeLocale->getLocalePrefix();

        /**
         * Temporary fix to remove _locale parameter from homepage link.
         * @todo Find a better solution for that.
         */
        $path = str_replace(['?_locale='.$activeLocale->getCode(), '&_locale='.$activeLocale->getCode()], '', $path);

        /** @var array $parts */
        $parts = parse_url($path);

        if (! isset($parts['path'])) {
            $parts['path'] = '/';
        }

        if ($backend ?? $this->isBackend()) {
            if ($localePrefix !== $this->getDefaultLocale()->getPathPrefix()) {
                $parts['path'] = str_replace(
                    $this->getBackendPrefix(),
                    $this->getBackendPrefix() . $localePrefix,
                    $parts['path']
                );
            }

            $parts['path'] = $this->getLocale()->getPathPrefix() . $parts['path'];
        } else {
            $parts['path'] = $this->getLocale()->getPathPrefix() . $localePrefix . $parts['path'];
        }

        return
            (isset($parts['scheme']) ? $parts['scheme'] . '://' : '')
            .($parts['host'] ?? '')
            .($parts['path'] ?? '')
            .(isset($parts['query']) ? '?' . $parts['query'] : '')
        ;
    }

    /**
     * @param string|null $locale
     * @return LocaleInterface
     * @throws LocaleNotExistsException
     */
    private function resolveLocale(?string $locale): LocaleInterface
    {
        if ($locale === null) {
            return $this->getDefaultLocale();
        } elseif ($locale instanceof LocaleInterface) {
            return $locale;
        } elseif (\is_string($locale)) {
            return $this->getLocaleByCode($locale);
        } else {
            throw new LocaleNotExistsException(sprintf('Locale must be a locale code string or instance of %s.', LocaleInterface::class));
        }
    }
}
