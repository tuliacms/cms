<?php

declare(strict_types=1);

namespace Tulia\Cms\Platform\Infrastructure\Composer\Extensions;

/**
 * @author Adam Banaszkiewicz
 */
final class ExtensionsStorage
{
    private string $extensionsFilepath;
    private array $extensions;

    public function __construct(
        private readonly string $rootDir,
    ) {
        $this->extensionsFilepath = realpath($this->rootDir.'/composer.extensions.json');
        $this->extensions = json_decode(file_get_contents($this->extensionsFilepath), true, JSON_THROW_ON_ERROR);
    }

    public function appendTheme(
        string $package,
        string $version,
        ExtensionSourceEnum $source,
        string $sourcePath,
    ): void {
        $manifest = json_decode(file_get_contents($sourcePath.'/manifest.json'), true, JSON_THROW_ON_ERROR);

        if ($source === ExtensionSourceEnum::VENDOR) {
            $info = [
                'source' => $source->value,
                'name' => $manifest['name'],
                'version' => $version,
                'installed-at' => date('Y-m-d H:i:s'),
                'path' => "/vendor/$package",
                'vendor-package-name' => $package,
                'entrypoint' => $this->resolveThemeEntrypoint($sourcePath),
                'manifest' => str_replace($this->rootDir, '', $sourcePath).'/manifest.json',
            ];
        } else {
            $info = [
                'source' => $source->value,
                'name' => $manifest['name'],
                'version' => $version,
                'installed-at' => date('Y-m-d H:i:s'),
                'path' => "/extension/theme/$package",
                'entrypoint' => $this->resolveThemeEntrypoint($sourcePath),
                'manifest' => str_replace($this->rootDir, '', $sourcePath).'/manifest.json',
            ];
        }

        $this->extensions['extra']['tuliacms']['themes'][$package] = $info;
    }

    public function appendModule(
        string $package,
        string $version,
        ExtensionSourceEnum $source,
        string $sourcePath,
    ): void {
        $manifest = json_decode(file_get_contents($sourcePath.'/manifest.json'), true, JSON_THROW_ON_ERROR);
        $entrypoint = $manifest['entrypoint'] ?? '';

        if ($source === ExtensionSourceEnum::VENDOR) {
            $info = [
                'source' => $source->value,
                'name' => $manifest['name'],
                'version' => $version,
                'installed-at' => date('Y-m-d H:i:s'),
                'path' => "/vendor/$package$entrypoint",
                'vendor-package-name' => $package,
                'manifest' => str_replace($this->rootDir, '', $sourcePath).'/manifest.json',
            ];
        } else {
            $info = [
                'source' => $source->value,
                'name' => $manifest['name'],
                'version' => $version,
                'installed-at' => date('Y-m-d H:i:s'),
                'path' => "/extension/module/$package$entrypoint",
                'manifest' => str_replace($this->rootDir, '', $sourcePath).'/manifest.json',
            ];
        }

        $this->extensions['extra']['tuliacms']['modules'][$package] = $info;
    }

    public function clearCollectionOf(ExtensionSourceEnum $sourceEnum): void
    {
        foreach ($this->extensions['extra']['tuliacms'] as $type => $elements) {
            foreach ($elements as $key => $elm) {
                if ($elm['source'] === $sourceEnum->value) {
                    unset($this->extensions['extra']['tuliacms'][$type][$key]);
                }
            }
        }
    }

    public function write(): void
    {
        file_put_contents($this->extensionsFilepath, json_encode($this->extensions, JSON_THROW_ON_ERROR | JSON_PRETTY_PRINT));
    }

    private function resolveThemeEntrypoint(string $packageDir): string
    {
        preg_match('#namespace ([a-zA-Z0-9\\\\]+);#i', file_get_contents($packageDir.'/Theme.php'), $matches);

        return $matches[1].'\\Theme';
    }

    public function removeTheme(mixed $package): void
    {
        $extensionsFilepath = realpath($this->rootDir.'/composer.extensions.json');
        $extensions = json_decode(file_get_contents($extensionsFilepath), true, JSON_THROW_ON_ERROR);

        unset($extensions['extra']['tuliacms']['themes'][$package]);

        file_put_contents($extensionsFilepath, json_encode($extensions, JSON_THROW_ON_ERROR | JSON_PRETTY_PRINT));
    }

    public function removeModule(mixed $package): void
    {
        $extensionsFilepath = realpath($this->rootDir.'/composer.extensions.json');
        $extensions = json_decode(file_get_contents($extensionsFilepath), true, JSON_THROW_ON_ERROR);

        unset($extensions['extra']['tuliacms']['modules'][$package]);

        file_put_contents($extensionsFilepath, json_encode($extensions, JSON_THROW_ON_ERROR | JSON_PRETTY_PRINT));
    }
}
