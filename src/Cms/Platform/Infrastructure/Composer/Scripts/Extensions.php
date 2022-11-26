<?php

declare(strict_types=1);

namespace Tulia\Cms\Platform\Infrastructure\Composer\Scripts;

use Composer\Installer\PackageEvent;
use Tulia\Cms\Platform\Infrastructure\Composer\Scripts\Exception\ManifestStructureInvalidException;

/**
 * @author Adam Banaszkiewicz
 */
final class Extensions
{
    private static ?array $uninstalledPackage = null;

    public static function install(PackageEvent $event): void
    {
        $package = $event->getOperation()->getPackage()->getName();
        $vendors = $event->getComposer()->getConfig()->get('vendor-dir');
        $packageDir = $vendors.'/'.$package;
        $manifestFilepath = $packageDir.'/manifest.json';

        if (false === is_file($manifestFilepath)) {
            $event->getIO()->write("# Tulia CMS: \e[94mPackage $package is not a Tulia CMS extension.\e[0m");
            return;
        }

        $manifest = json_decode(file_get_contents($manifestFilepath), true, JSON_THROW_ON_ERROR);

        self::validateManifest($manifest);

        $extensionsFilepath = realpath(dirname($vendors).'/composer.extensions.json');
        $extensions = json_decode(file_get_contents($extensionsFilepath), true, JSON_THROW_ON_ERROR);

        if ($manifest['type'] === 'theme') {
            $extensions['extra']['tuliacms']['themes'][$package] = [
                'source' => 'vendor',
                'name' => $manifest['name'],
                'version' => $event->getOperation()->getPackage()->getVersion(),
                'path' => "vendor/$package",
                'vendor-package-name' => $package,
                'entrypoint' => self::resolveEntrypoint($packageDir),
                'manifest' => "vendor/$package/manifest.json",
            ];
        }

        file_put_contents($extensionsFilepath, json_encode($extensions, JSON_THROW_ON_ERROR | JSON_PRETTY_PRINT));

        $event->getIO()->write("# Tulia CMS: \e[1;92mPackage $package is installed as a Extension\e[0m");
    }

    public static function update(PackageEvent $event): void
    {
        //var_dump($event);exit;
        $event->getComposer()->getPackage()->getExtra();
    }

    public static function preuninstall(PackageEvent $event): void
    {
        $package = $event->getOperation()->getPackage()->getName();
        $vendors = $event->getComposer()->getConfig()->get('vendor-dir');
        $packageDir = $vendors.'/'.$package;
        $manifestFilepath = $packageDir.'/manifest.json';

        if (false === is_file($manifestFilepath)) {
            return;
        }

        self::$uninstalledPackage = [
            'manifest' => json_decode(file_get_contents($manifestFilepath), true, JSON_THROW_ON_ERROR),
            'name' => $package,
        ];
    }

    public static function uninstall(PackageEvent $event): void
    {
        $vendors = $event->getComposer()->getConfig()->get('vendor-dir');

        if (!self::$uninstalledPackage) {
            return;
        }

        $manifest = self::$uninstalledPackage['manifest'];
        $package = self::$uninstalledPackage['name'];

        $extensionsFilepath = realpath(dirname($vendors).'/composer.extensions.json');
        $extensions = json_decode(file_get_contents($extensionsFilepath), true, JSON_THROW_ON_ERROR);

        if ($manifest['type'] === 'theme') {
            unset($extensions['extra']['tuliacms']['themes'][$package]);
        }

        file_put_contents($extensionsFilepath, json_encode($extensions, JSON_THROW_ON_ERROR | JSON_PRETTY_PRINT));

        $event->getIO()->write("# Tulia CMS: \e[1;92mPackage $package was uninstalled from system\e[0m");
    }

    /**
     * @throws ManifestStructureInvalidException
     */
    private static function validateManifest(array $manifest): void
    {
        if (!isset($manifest['type'])) {
            throw ManifestStructureInvalidException::missingField('type');
        }
        if (!in_array($manifest['type'], ['theme', 'module'])) {
            throw ManifestStructureInvalidException::invalidValue('type', $manifest['type'], ['theme', 'module']);
        }
        if (!isset($manifest['name'])) {
            throw ManifestStructureInvalidException::missingField('name');
        }
    }

    private static function resolveEntrypoint(string $packageDir): string
    {
        preg_match('#namespace ([a-zA-Z0-9\\\\]+);#i', file_get_contents($packageDir.'/Theme.php'), $matches);

        return $matches[1].'\\Theme';
    }
}
