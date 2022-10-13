<?php

declare(strict_types=1);

namespace Tulia\Cms\Website\Domain\WriteModel\Service;

use Tulia\Cms\Platform\Infrastructure\Framework\Routing\Exception\LocaleNotExistsException;
use Tulia\Cms\Platform\Infrastructure\Framework\Routing\Website\LocaleInterface;

/**
 * @author Adam Banaszkiewicz
 */
interface StorageInterface
{
    /**
     * @return LocaleInterface[]
     */
    public function all(): array;

    /**
     * @throws LocaleNotExistsException
     */
    public function getByCode(string $code): LocaleInterface;
}
