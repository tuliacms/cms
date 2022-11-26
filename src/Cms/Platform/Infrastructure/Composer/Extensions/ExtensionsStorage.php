<?php

declare(strict_types=1);

namespace Tulia\Cms\Platform\Infrastructure\Composer\Extensions;

/**
 * @author Adam Banaszkiewicz
 */
final class ExtensionsStorage
{
    public function __construct(
        private readonly string $rootDir,
    ) {
    }

    public function appendTheme(
        string $package,
        string $version,
        ExtensionSourceEnum $source,
        string $sourcePath,
    ): void {
        $extensionsFilepath = realpath($this->rootDir.'/composer.extensions.json');
        $extensions = json_decode(file_get_contents($extensionsFilepath), true, JSON_THROW_ON_ERROR);
        $manifest = json_decode(file_get_contents($sourcePath.'/manifest.json'), true, JSON_THROW_ON_ERROR);

        if ($source === ExtensionSourceEnum::VENDOR) {
            $info = [
                'source' => $source->value,
                'name' => $manifest['name'],
                'version' => $version,
                'path' => "/vendor/$package",
                'vendor-package-name' => $package,
                'entrypoint' => $this->resolveEntrypoint($sourcePath),
                'manifest' => str_replace($this->rootDir, '', $sourcePath).'/manifest.json',
            ];
        } else {
            $info = [
                'source' => $source->value,
                'name' => $manifest['name'],
                'version' => $version,
                'path' => "/extension/theme/$package",
                'entrypoint' => $this->resolveEntrypoint($sourcePath),
                'manifest' => str_replace($this->rootDir, '', $sourcePath).'/manifest.json',
            ];
        }

        $extensions['extra']['tuliacms']['themes'][$package] = $info;

        file_put_contents($extensionsFilepath, json_encode($extensions, JSON_THROW_ON_ERROR | JSON_PRETTY_PRINT));
    }

    private function resolveEntrypoint(string $packageDir): string
    {
        preg_match('#namespace ([a-zA-Z0-9\\\\]+);#i', file_get_contents($packageDir.'/Theme.php'), $matches);

        return $matches[1].'\\Theme';
    }

    public function removeTheme(mixed $package)
    {
        $extensionsFilepath = realpath($this->rootDir.'/composer.extensions.json');
        $extensions = json_decode(file_get_contents($extensionsFilepath), true, JSON_THROW_ON_ERROR);

        unset($extensions['extra']['tuliacms']['themes'][$package]);

        file_put_contents($extensionsFilepath, json_encode($extensions, JSON_THROW_ON_ERROR | JSON_PRETTY_PRINT));
    }
}
