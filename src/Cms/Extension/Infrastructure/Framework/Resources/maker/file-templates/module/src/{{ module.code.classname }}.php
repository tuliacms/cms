<?php

declare(strict_types=1);

namespace {{ module.code.namespaced }};

use Tulia\Cms\Extension\Infrastructure\Framework\Module\AbstractModule;

class {{ module.code.classname }} extends AbstractModule
{
    public function getPath(): string
    {
        return dirname(__DIR__);
    }
}
