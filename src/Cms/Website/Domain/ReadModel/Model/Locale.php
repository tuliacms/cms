<?php

declare(strict_types=1);

namespace Tulia\Cms\Website\Domain\ReadModel\Model;

/**
 * @author Adam Banaszkiewicz
 */
class Locale
{
    protected string $code;
    protected string $domain;
    protected string $domainDevelopment;
    protected ?string $localePrefix = null;
    protected ?string $pathPrefix = null;
    protected string $sslMode;
    protected bool $isDefault = false;
    protected bool $active = true;

    public function __construct(
        string $code,
        string $domain,
        string $developmentDomain,
        ?string $localePrefix = null,
        ?string $pathPrefix = null,
        string $sslMode = 'ALLOWED_BOTH',
        bool $active = true,
        bool $isDefault = false,
    ) {
        $this->code = $code;
        $this->domain = $domain;
        $this->domainDevelopment = $developmentDomain;
        $this->localePrefix = $localePrefix;
        $this->pathPrefix = $pathPrefix;
        $this->sslMode = $sslMode;
        $this->active = $active;
        $this->isDefault = $isDefault;
    }

    public static function fromArray(array $data = []): self
    {
        return new self(
            $data['code'],
            $data['domain'],
            $data['domain_development'],
            $data['locale_prefix'] ?? null,
            $data['path_prefix'] ?? null,
            $data['ssl_mode'] ?? 'ALLOWED_BOTH',
            (bool) ($data['active'] ?? true),
            (bool) ($data['is_default'] ?? true),
        );
    }

    public function toArray(): array
    {
        return [
            'code' => $this->code,
            'domain' => $this->domain,
            'domain_development' => $this->domainDevelopment,
            'locale_prefix' => $this->localePrefix,
            'path_prefix' => $this->pathPrefix,
            'ssl_mode' => $this->sslMode,
            'active' => $this->active,
            'is_default' => $this->isDefault,
        ];
    }

    public function __toString(): string
    {
        return $this->code;
    }

    public function getCode(): string
    {
        return $this->code;
    }

    public function getLanguage(): string
    {
        return strpos($this->code, '_') ? explode('_', $this->code)[0] : $this->code;
    }

    public function getDomain(): string
    {
        return $this->domain;
    }

    public function getLocalePrefix(): ?string
    {
        return $this->localePrefix;
    }

    public function getPathPrefix(): ?string
    {
        return $this->pathPrefix;
    }

    public function getSslMode(): string
    {
        return $this->sslMode;
    }

    public function isDefault(): bool
    {
        return $this->isDefault;
    }

    public function isActive(): bool
    {
        return $this->active;
    }

    public function getDomainDevelopment(): string
    {
        return $this->domainDevelopment;
    }
}
