<?php

declare(strict_types=1);

namespace Tulia\Cms\Content\Type\Domain\WriteModel\Service;

/**
 * @author Adam Banaszkiewicz
 */
interface ContentTypeExistanceDetectorInterface
{
    public function exists(string $code): bool;
}
