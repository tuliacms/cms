<?php

declare(strict_types=1);

namespace Tulia\Cms\Website\Domain\WriteModel\Model;

use Tulia\Cms\Platform\Infrastructure\Framework\Routing\Website\SslModeEnum;

/**
 * @author Adam Banaszkiewicz
 * @final
 */
class Locale
{
    private string $id;

    public function __construct(
        private Website $website,
        private string $code,
        private string $domain,
        private ?string $domainDevelopment = null,
        private ?string $localePrefix = null,
        private ?string $pathPrefix = null,
        private SslModeEnum $sslMode = SslModeEnum::ALLOWED_BOTH,
        private bool $isDefault = false,
    ) {
    }

    public function __toString(): string
    {
        return $this->code;
    }

    public function toArray(): array
    {
        return [
            'code' => $this->code,
            'domain' => $this->domain,
            'domain_development' => $this->domainDevelopment,
            'locale_prefix' => $this->localePrefix,
            'path_prefix' => $this->pathPrefix,
            'ssl_mode' => $this->sslMode->value,
            'is_default' => $this->isDefault,
        ];
    }

    public function getCode(): string
    {
        return $this->code;
    }

    public function setCode(string $code): void
    {
        $this->code = $code;
    }

    public function getDomain(): string
    {
        return $this->domain;
    }

    public function setDomain(string $domain): void
    {
        $this->domain = $domain;
    }

    public function getDomainDevelopment(): ?string
    {
        return $this->domainDevelopment;
    }

    public function setDomainDevelopment(?string $domainDevelopment): void
    {
        $this->domainDevelopment = $domainDevelopment;
    }

    public function getLocalePrefix(): ?string
    {
        return $this->localePrefix;
    }

    public function setLocalePrefix(?string $localePrefix): void
    {
        $this->localePrefix = $localePrefix;
    }

    public function getPathPrefix(): ?string
    {
        return $this->pathPrefix;
    }

    public function setPathPrefix(?string $pathPrefix): void
    {
        $this->pathPrefix = $pathPrefix;
    }

    public function getSslMode(): SslModeEnum
    {
        return $this->sslMode;
    }

    public function setSslMode(SslModeEnum $sslMode): void
    {
        $this->sslMode = $sslMode;
    }

    public function isDefault(): bool
    {
        return $this->isDefault;
    }

    public function setIsDefault(bool $isDefault): void
    {
        $this->isDefault = $isDefault;
    }
}
