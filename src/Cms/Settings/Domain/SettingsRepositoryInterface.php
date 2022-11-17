<?php

declare(strict_types=1);

namespace Tulia\Cms\Settings\Domain;

use Tulia\Cms\Settings\Domain\Model\Settings;

/**
 * @author Adam Banaszkiewicz
 */
interface SettingsRepositoryInterface
{
    public function get(string $websiteId, string $locale, string $defaultLocale): Settings;
    public function save(Settings $settings): void;
}
