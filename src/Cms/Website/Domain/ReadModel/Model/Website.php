<?php

declare(strict_types=1);

namespace Tulia\Cms\Website\Domain\ReadModel\Model;

use Tulia\Cms\Website\Domain\WriteModel\Exception\LocaleNotExistsException;

/**
 * @author Adam Banaszkiewicz
 */
class Website
{
    protected string $id;
    protected string $name;
    protected string $backendPrefix;
    protected bool $enabled = true;
    /** @var Locale[] */
    protected array $locales = [];

    public function __construct(string $id, array $locales, string $backendPrefix, string $name, bool $enabled = true)
    {
        $this->id = $id;
        $this->backendPrefix = $backendPrefix;
        $this->name = $name;
        $this->enabled = $enabled;

        foreach ($locales as $locale) {
            $this->addLocale($locale);
        }
    }

    public static function fromArray(array $data = []): self
    {
        return new self(
            $data['id'],
            $data['locales'] ?? [],
            $data['backend_prefix'] ?? '/administrator',
            $data['name'] ?? '',
            $data['enabled'] ?? true
        );
    }

    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'backend_prefix' => $this->backendPrefix,
            'enabled' => $this->enabled,
            'locales' => array_map(
                static fn($l) => $l->toArray(),
                $this->locales
            ),
        ];
    }

    public function getId(): string
    {
        return $this->id;
    }

    public function setId(string $id): void
    {
        $this->id = $id;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getBackendPrefix(): string
    {
        return $this->backendPrefix;
    }

    public function isEnabled(): bool
    {
        return $this->enabled;
    }

    /**
     * @return Locale[]
     */
    public function getLocales(): array
    {
        return $this->locales;
    }

    public function addLocale(Locale $locale): void
    {
        $this->locales[] = $locale;
    }

    /**
     * @return Locale
     * @throws LocaleNotExistsException
     */
    public function getDefaultLocale(): Locale
    {
        foreach ($this->locales as $locale) {
            if ($locale->isDefault()) {
                return $locale;
            }
        }

        throw new LocaleNotExistsException('Default locale not exists for this website.');
    }

    /**
     * @param string $code
     * @return Locale
     * @throws LocaleNotExistsException
     */
    public function getLocaleByCode(string $code): Locale
    {
        foreach ($this->locales as $locale) {
            if ($locale->getCode() === $code) {
                return $locale;
            }
        }

        throw new LocaleNotExistsException(sprintf('Locale "%s" not exists in this website.', $code));
    }

    /**
     * @param null|string|Locale $locale
     * @return string
     * @throws LocaleNotExistsException
     */
    public function getAddress($locale = null): string
    {
        $locale = $this->resolveLocale($locale);

        if ($locale->getSslMode() === 'FORCE_SSL') {
            return 'https://' . $locale->getDomain() . $locale->getPathPrefix() . $locale->getLocalePrefix() . '/';
        }

        return 'http://' . $locale->getDomain() . $locale->getPathPrefix() . $locale->getLocalePrefix() . '/';
    }

    /**
     * @param null|string|Locale $locale
     * @return string
     * @throws LocaleNotExistsException
     */
    public function getBackendAddress($locale = null): string
    {
        $locale = $this->resolveLocale($locale);

        if ($locale->getSslMode() === 'FORCE_SSL') {
            return 'https://' . $locale->getDomain() . $locale->getPathPrefix() . $this->backendPrefix . $locale->getLocalePrefix() . '/';
        }

        return 'http://' . $locale->getDomain() . $locale->getPathPrefix() . $this->backendPrefix . $locale->getLocalePrefix() . '/';
    }

    /**
     * @param null|string|Locale $locale
     * @return Locale
     * @throws LocaleNotExistsException
     */
    private function resolveLocale($locale): Locale
    {
        if ($locale === null) {
            return $this->getDefaultLocale();
        } elseif ($locale instanceof Locale) {
            return $locale;
        } elseif (\is_string($locale)) {
            return $this->getLocaleByCode($locale);
        } else {
            throw new LocaleNotExistsException(sprintf('Locale must be a locale code string or instance of %s.', Locale::class));
        }
    }
}
