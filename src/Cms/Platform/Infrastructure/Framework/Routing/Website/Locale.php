<?php

declare(strict_types=1);

namespace Tulia\Cms\Platform\Infrastructure\Framework\Routing\Website;

/**
 * @author Adam Banaszkiewicz
 */
class Locale implements LocaleInterface
{
    private readonly string $language;
    private readonly string $region;

    public function __construct(
        private readonly string $code,
        private readonly string $domain,
        private readonly ?string $localePrefix = null,
        private readonly ?string $pathPrefix = null,
        private readonly SslModeEnum $sslMode = SslModeEnum::ALLOWED_BOTH,
        private readonly bool $active = true,
    ) {
        if (strpos($code, '_') !== false) {
            [$this->language, $this->region] = explode('_', $code);
        } else {
            $this->language = $this->region = $code;
        }
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
        return $this->language;
    }

    public function getRegion(): string
    {
        return $this->region;
    }

    public function getDomain(): string
    {
        return $this->domain;
    }

    public function getSslMode(): string
    {
        return $this->sslMode->value;
    }

    public function getPathPrefix(): ?string
    {
        return $this->pathPrefix;
    }

    public function getLocalePrefix(): ?string
    {
        return $this->localePrefix;
    }

    public function isActive(): bool
    {
        return $this->active;
    }
}
