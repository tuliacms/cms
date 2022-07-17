<?php

declare(strict_types=1);

namespace Tulia\Component\Routing\Website;

use Tulia\Component\Routing\Enum\SslModeEnum;
use Tulia\Component\Routing\Website\Exception\LocaleNotExistsException;
use Tulia\Component\Routing\Website\Locale\LocaleInterface;

/**
 * @author Adam Banaszkiewicz
 */
class Website implements WebsiteInterface
{
    private string $backendPrefix;
    /** @var LocaleInterface[] */
    private array $locales = [];
    private LocaleInterface $defaultLocale;
    private LocaleInterface $activeLocale;
    private bool $isBackend;
    private string $basepath;

    /** @param LocaleInterface[] $locales */
    public function __construct(
        string $backendPrefix,
        bool $isBackend,
        string $basepath,
        array $locales,
        string $defaultLocale,
        string $activeLocale
    ) {
        $this->backendPrefix = $backendPrefix;
        $this->isBackend = $isBackend;
        $this->basepath = $basepath;

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

    public function getBasepath(): string
    {
        return $this->basepath;
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
