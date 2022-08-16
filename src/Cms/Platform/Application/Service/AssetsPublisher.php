<?php

declare(strict_types=1);

namespace Tulia\Cms\Platform\Application\Service;

use Symfony\Component\Filesystem\Filesystem;

/**
 * @author Adam Banaszkiewicz
 */
class AssetsPublisher
{
    public function __construct(
        private string $publicDir,
        private array $assetsPublicPaths = []
    ) {
    }

    public function publishRegisteredAssets(): void
    {
        foreach ($this->assetsPublicPaths as $path) {
            $this->publish($path['source'], $path['target']);
        }
    }

    public function publish(string $source, string $targetname): bool
    {
        $target = $this->publicDir.'/assets'.$targetname;

        if (file_exists($source) === false) {
            return false;
        }

        $fs = new Filesystem();
        $fs->mirror($source, $target, null, [
            'override' => true,
            'delete' => true,
        ]);

        return true;
    }
}
