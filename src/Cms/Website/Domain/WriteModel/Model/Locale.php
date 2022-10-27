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
        public bool $enabled = true,
        public bool $isDefault = false,
    ) {
    }

    public function __toString(): string
    {
        return $this->code;
    }

    public function isA(string $code): bool
    {
        return $this->code === $code;
    }

    public function enable(): bool
    {
        if ($this->enabled === false) {
            $this->enabled = true;
            return true;
        }

        return false;
    }

    public function disable(): bool
    {
        if ($this->enabled === true) {
            $this->enabled = false;
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
