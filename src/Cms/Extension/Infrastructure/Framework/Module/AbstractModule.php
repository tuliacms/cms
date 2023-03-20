<?php

declare(strict_types=1);

namespace Tulia\Cms\Extension\Infrastructure\Framework\Module;

use Symfony\Component\HttpKernel\Bundle\Bundle;

/**
 * @author Adam Banaszkiewicz
 */
abstract class AbstractModule extends Bundle
{
    public function getPath(): string
    {
        throw new \RuntimeException(sprintf('Please overwrite a %s method in %s module.', __METHOD__, get_class($this)));
    }
}
