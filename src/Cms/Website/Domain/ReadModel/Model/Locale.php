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
    protected ?string $developmentDomain = null;
    protected ?string $localePrefix = null;
    protected ?string $pathPrefix = null;
    protected string $sslMode;
    protected bool $isDefault = false;

    public function __construct(
        string $code,
        string $domain,
        ?string $developmentDomain = null,
        ?string $localePrefix = null,
        ?string $pathPrefix = null,
        string $sslMode = 'ALLOWED_BOTH',
        bool $isDefault = false
    ) {
        $this->code = $code;
        $this->domain = $domain;
        $this->developmentDomain = $developmentDomain;
        $this->localePrefix = $localePrefix;
        $this->pathPrefix = $pathPrefix;
        $this->sslMode = $sslMode;
        $this->isDefault = $isDefault;
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

    public function getDevelopmentDomain(): ?string
    {
        return $this->developmentDomain;
    }
}
