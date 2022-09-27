<?php

declare(strict_types=1);

namespace Tulia\Component\Theme\Configuration;

/**
 * @author Adam Banaszkiewicz
 */
class Configuration implements ConfigurationInterface
{
    private array $assets = [];
    private array $imageSizeList = [];
    private array $widgetSpaceList = [];
    private array $widgetStyleList = [];
    private array $customizer = [];
    private string $translationDomain = 'messages';
    private string $nodeContentField = 'content';

    public function getWidgetStyles(): array
    {
        return $this->widgetStyleList;
    }

    public function merge(ConfigurationInterface $configuration): void
    {
        $this->assets = array_merge($this->assets, $configuration->assets);
        $this->imageSizeList = array_merge($this->imageSizeList, $configuration->imageSizeList);
        $this->widgetSpaceList = array_merge($this->widgetSpaceList, $configuration->widgetSpaceList);
        $this->widgetStyleList = array_merge($this->widgetStyleList, $configuration->widgetStyleList);
        $this->customizer = array_merge($this->customizer, $configuration->customizer);
        $this->translationDomain = $configuration->translationDomain;
        $this->nodeContentField = $configuration->nodeContentField;
    }

    public function addWidgetStyle(string $name, string $label): void
    {
        $this->widgetStyleList[$name] = [
            'name' => $name,
            'label' => $label,
        ];
    }

    public function addWidgetSpace(string $name, string $label): void
    {
        $this->widgetSpaceList[$name] = [
            'name' => $name,
            'label' => $label,
        ];
    }

    public function getWidgetSpaces(): array
    {
        return $this->widgetSpaceList;
    }

    public function addImageSize(string $name, ?int $width, ?int $height, string $mode): void
    {
        $this->imageSizeList[$name] = [
            'name' => $name,
            'width' => $width,
            'height' => $height,
            'mode' => $mode,
        ];
    }

    public function getImageSizes(): array
    {
        return $this->imageSizeList;
    }

    public function addAsset(string $name): void
    {
        $this->assets[] = $name;
    }

    public function getAssets(): array
    {
        return $this->assets;
    }

    public function setNodeContentField(string $nodeContentField): void
    {
        $this->nodeContentField = $nodeContentField;
    }

    public function getNodeContentField(): string
    {
        return $this->nodeContentField;
    }

    public function setTranslationDomain(string $translationDomain): void
    {
        $this->translationDomain = $translationDomain;
    }

    public function getTranslationDomain(): string
    {
        return $this->translationDomain;
    }

    public function setCustomizerVariable(string $name, mixed $value): void
    {
        $this->customizer[$name] = $value;
    }

    public function getCustomizerVariable(string $name, mixed $default = null): mixed
    {
        return $this->customizer[$name] ?? $default;
    }
}
