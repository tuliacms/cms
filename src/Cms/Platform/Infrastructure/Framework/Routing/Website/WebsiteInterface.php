<?php

declare(strict_types=1);

namespace Tulia\Cms\Platform\Infrastructure\Framework\Routing\Website;

/**
 * @author Adam Banaszkiewicz
 */
interface WebsiteInterface
{
    public function getId(): string;
    public function getName(): string;
    public function getBackendPrefix(): string;
    public function getLocale(): LocaleInterface;
    /** @return LocaleInterface[] */
    public function getLocales(): array;
    public function getDefaultLocale(): LocaleInterface;
    public function getLocaleByCode(string $code): LocaleInterface;
    public function getAddress(?string $localeCode = null): string;
    public function getBackendAddress(?string $localeCode = null): string;
    public function isBackend(): bool;
    public function isDefaultLocale(): bool;
    public function isActive(): bool;
    public function getLocaleCodes(): array;
    public function prepareRequestUriToRoutingMatching(string $requestUri): string;
}
