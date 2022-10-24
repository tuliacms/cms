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
        public readonly string $code,
        public string $domain,
        public ?string $domainDevelopment = null,
        public ?string $localePrefix = null,
        public ?string $pathPrefix = null,
        public SslModeEnum $sslMode = SslModeEnum::ALLOWED_BOTH,
        public bool $active = true,
        public bool $isDefault = false,
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
            'active' => $this->active,
        ];
    }

    public function isA(string $code): bool
    {
        return $this->code === $code;
    }

    public function activate(): bool
    {
        if ($this->active === false) {
            $this->active = true;
            return true;
        }

        return false;
    }

    public function deactivate(): bool
    {
        if ($this->active === true) {
            $this->active = false;
            return true;
        }

        return false;
    }

    public function getLocalePrefix(): ?string
    {
        return $this->localePrefix;
    }

    public function getPathPrefix(): ?string
    {
        return $this->pathPrefix;
    }
}
