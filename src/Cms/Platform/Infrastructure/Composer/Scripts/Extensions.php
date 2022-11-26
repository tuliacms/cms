<?php

declare(strict_types=1);

namespace Tulia\Cms\Platform\Infrastructure\Composer\Scripts;

use Composer\Installer\PackageEvent;
use Tulia\Cms\Platform\Infrastructure\Composer\Extensions\ExtensionSourceEnum;
use Tulia\Cms\Platform\Infrastructure\Composer\Extensions\ExtensionsStorage;
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

        $extensions = new ExtensionsStorage(dirname($vendors));

        if ($manifest['type'] === 'theme') {
            $extensions->appendTheme(
                $package,
                $event->getOperation()->getPackage()->getVersion(),
                ExtensionSourceEnum::VENDOR,
                $packageDir,
            );
        }

        $event->getIO()->write("# Tulia CMS: \e[1;92mPackage $package is installed as a Extension\e[0m");
    }

    public static function update(PackageEvent $event): void
    {
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

        $extensions = new ExtensionsStorage(dirname($vendors));

        if ($manifest['type'] === 'theme') {
            $extensions->removeTheme($package);
        }

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
}
