<?php

declare(strict_types=1);

namespace Tulia\Component\Routing\Website\Locale;

use Tulia\Component\Routing\Enum\SslModeEnum;

/**
 * @author Adam Banaszkiewicz
 */
class Locale implements LocaleInterface
{
    protected string $code;

    protected string $language;

    protected string $region;

    protected string $domain;

    protected ?string $localePrefix = null;

    protected ?string $pathPrefix = null;

    protected string $sslMode;

    public function __construct(
        string $code,
        string $domain,
        string $localePrefix = null,
        string $pathPrefix = null,
        string $sslMode = SslModeEnum::ALLOWED_BOTH
    ) {
        $this->code = $code;
        $this->domain = $domain;
        $this->localePrefix = $localePrefix;
        $this->pathPrefix = $pathPrefix;
        $this->sslMode = $sslMode;

        if (strpos($code, '_') !== false) {
            list($this->language, $this->region) = explode('_', $code);
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
        return $this->sslMode;
    }

    public function getPathPrefix(): ?string
    {
        return $this->pathPrefix;
    }

    public function getLocalePrefix(): ?string
    {
        return $this->localePrefix;
    }
}
