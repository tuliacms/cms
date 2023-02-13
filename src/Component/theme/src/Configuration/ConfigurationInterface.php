<?php

declare(strict_types=1);

namespace Tulia\Component\Theme\Configuration;

/**
 * @author Adam Banaszkiewicz
 */
interface ConfigurationInterface
{
    public function merge(ConfigurationInterface $configuration): void;

    public function setTranslationDomain(string $translationDomain): void;
    public function getTranslationDomain(): string;
    public function getWidgetSpaces(): array;
    public function getWidgetStyles(): array;
    public function addWidgetStyle(string $name, string $label): void;
    public function addWidgetSpace(string $name, string $label): void;
    public function addMenuSpace(string $name, string $label): void;
    public function getMenuSpaces(): array;
    public function addImageSize(string $name, ?int $width, ?int $height, string $mode): void;
    public function getImageSizes(): array;
    public function addAsset(string $name): void;
    public function getAssets(): array;
    public function setNodeContentField(string $nodeContentField): void;
    public function getNodeContentField(): string;
    public function setCssFramework(string $cssFramework): void;
    public function getCssFramework(): string;
    public function setCustomizerVariable(string $name, mixed $value): void;
    public function getCustomizerVariable(string $name, mixed $default = null): mixed;
    public function setVariables(array $variables): void;
    public function getVariables(): array;
}
