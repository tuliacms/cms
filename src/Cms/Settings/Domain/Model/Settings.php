<?php

declare(strict_types=1);

namespace Tulia\Cms\Settings\Domain\Model;

use Tulia\Cms\Settings\Domain\Event\SettingsUpdated;
use Tulia\Cms\Shared\Domain\WriteModel\Model\AbstractAggregateRoot;

/**
 * @author Adam Banaszkiewicz
 */
final class Settings extends AbstractAggregateRoot
{
    public function __construct(
        private string $websiteId,
        private string $locale,
        private string $defaultLocale,
        private array $settings,
    ) {
    }

    public function getWebsiteId(): string
    {
        return $this->websiteId;
    }

    public function getLocale(): string
    {
        return $this->locale;
    }

    public function getDefaultLocale(): string
    {
        return $this->defaultLocale;
    }

    public function export(): array
    {
        return $this->settings;
    }

    public function update(array $settings): void
    {
        foreach ($settings as $key => $val) {
            $this->settings[$key] = $val;
        }

        $this->recordThat(new SettingsUpdated(array_keys($settings)));
    }
}
