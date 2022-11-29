<?php

declare(strict_types=1);

namespace Tulia\Cms\Platform\Infrastructure\Composer\Scripts;

use Composer\Script\Event;
use Tulia\Cms\Platform\Infrastructure\Composer\Extensions\ExtensionSourceEnum;
use Tulia\Cms\Platform\Infrastructure\Composer\Extensions\ExtensionsStorage;
use Tulia\Cms\Platform\Infrastructure\Composer\Scripts\Exception\ManifestStructureInvalidException;

/**
 * @author Adam Banaszkiewicz
 */
final class Extensions
{
    public static function discover(Event $event): void
    {
        $packages = $event->getComposer()->getRepositoryManager()->getLocalRepository()->getPackages();
        $vendors = $event->getComposer()->getConfig()->get('vendor-dir');
        $extensions = new ExtensionsStorage(dirname($vendors));
        $extensions->clearCollection();

        foreach ($packages as $package) {
            $packageDir = $vendors.'/'.$package->getName();
            $manifestFilepath = $packageDir.'/manifest.json';

            if (false === is_file($manifestFilepath)) {
                continue;
            }

            $manifest = json_decode(file_get_contents($manifestFilepath), true, JSON_THROW_ON_ERROR);
            self::validateManifest($manifest);

            if ($manifest['type'] === 'theme') {
                $extensions->appendTheme(
                    $package->getName(),
                    $package->getVersion(),
                    ExtensionSourceEnum::VENDOR,
                    $packageDir,
                );
            } elseif ($manifest['type'] === 'module') {
                $extensions->appendModule(
                    $package->getName(),
                    $package->getVersion(),
                    ExtensionSourceEnum::VENDOR,
                    $packageDir,
                );
            }
        }

        $extensions->write();
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
