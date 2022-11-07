<?php

declare(strict_types=1);

namespace Tulia\Cms\Filemanager\Domain\ReadModel\Service;

/**
 * @author Adam Banaszkiewicz
 */
interface ImageUrlGeneratorInterface
{
    public function generate(string $id, array $params = []): string;
}
