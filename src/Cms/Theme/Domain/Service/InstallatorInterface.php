<?php

declare(strict_types=1);

namespace Tulia\Cms\Theme\Domain\Service;

/**
 * @author Adam Banaszkiewicz
 */
interface InstallatorInterface
{
    public function install(\SplFileInfo $file): void;
}
