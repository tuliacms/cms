<?php

declare(strict_types=1);

namespace Tulia\Cms\Platform\Infrastructure\Framework\Routing\Website;

/**
 * @author Adam Banaszkiewicz
 */
interface LocaleInterface
{
    public function __toString(): string;
    public function getCode(): string;
    public function getLanguage(): string;
    public function getRegion(): string;
    public function getDomain(): string;
    public function getSslMode(): string;
    public function getPathPrefix(): ?string;
    public function getLocalePrefix(): ?string;
    public function isActive(): bool;
    public function isDefault(): bool;
}
