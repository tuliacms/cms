<?php

declare(strict_types=1);

namespace Tulia\Cms\Website\Domain\WriteModel\Service;

use Tulia\Cms\Platform\Infrastructure\Framework\Routing\Website\LocaleInterface;

/**
 * @author Adam Banaszkiewicz
 */
interface LocaleStorageInterface
{
    /**
     * @return LocaleInterface[]
     */
    public function all(): array;
}
