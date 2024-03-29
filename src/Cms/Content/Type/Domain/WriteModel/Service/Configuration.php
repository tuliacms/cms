<?php

declare(strict_types=1);

namespace Tulia\Cms\Content\Type\Domain\WriteModel\Service;

/**
 * @author Adam Banaszkiewicz
 */
class Configuration
{
    private array $configuration;

    public function __construct(array $configuration)
    {
        $this->configuration = $configuration;
    }

    public function typeExists(string $type): bool
    {
        return isset($this->configuration[$type]);
    }

    public function getTypes(): array
    {
        return array_keys($this->configuration);
    }

    public function getConfigurableTypes(): array
    {
        $result = [];

        foreach ($this->configuration as $type => $info) {
            if ($info['configurable']) {
                $result[] = $type;
            }
        }

        return $result;
    }

    public function isConfigurable(string $type): bool
    {
        return $this->configuration[$type]['configurable'];
    }

    public function getController(string $type): string
    {
        return $this->configuration[$type]['controller'];
    }

    public function getLayoutBuilder(string $type): string
    {
        return $this->configuration[$type]['layout_builder'];
    }

    public function isMultilingual(string $type): bool
    {
        return $this->configuration[$type]['multilingual'];
    }
}
