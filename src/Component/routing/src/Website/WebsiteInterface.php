<?php

declare(strict_types=1);

namespace Tulia\Component\Routing\Website;

use Tulia\Component\Routing\Website\Locale\LocaleInterface;

/**
 * @author Adam Banaszkiewicz
 */
interface WebsiteInterface
{
    public function getBackendPrefix(): string;
    public function getLocale(): LocaleInterface;
    /** @return LocaleInterface[] */
    public function getLocales(): array;
    public function getDefaultLocale(): LocaleInterface;
    public function getLocaleByCode(string $code): LocaleInterface;
    public function getAddress(?string $localeCode = null): string;
    public function getBackendAddress(?string $localeCode = null): string;
    public function isBackend(): bool;
    public function getBasepath(): string;
    public function isDefaultLocale(): bool;
}
