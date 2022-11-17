<?php

declare(strict_types=1);

namespace Tulia\Cms\Settings\Infrastructure\Cms\Options;

use Tulia\Cms\Options\Domain\WriteModel\OptionsRepositoryInterface;
use Tulia\Cms\Settings\Domain\Model\Settings;
use Tulia\Cms\Settings\Domain\SettingsRepositoryInterface;

/**
 * @author Adam Banaszkiewicz
 */
final class OptionsAwareSettingsRepository implements SettingsRepositoryInterface
{
    public function __construct(
        private readonly OptionsRepositoryInterface $options,
    ) {
    }

    public function get(string $websiteId, string $locale, string $defaultLocale): Settings
    {
        $options = [];

        foreach ($this->options->getAllForWebsite($websiteId) as $option) {
            $options[$option->getName()] = $option->getValue($locale);
        }

        return new Settings($websiteId, $locale, $defaultLocale, $options);
    }

    public function save(Settings $settings): void
    {
        foreach ($settings->export() as $name => $value) {
            $option = $this->options->get($name, $settings->getWebsiteId());
            $option->setValue($value, $settings->getLocale(), $settings->getDefaultLocale());
            $this->options->save($option);
        }
    }
}
