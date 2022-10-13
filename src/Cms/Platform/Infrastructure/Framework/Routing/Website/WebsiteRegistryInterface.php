<?php

declare(strict_types=1);

namespace Tulia\Cms\Platform\Infrastructure\Framework\Routing\Website;

/**
 * @author Adam Banaszkiewicz
 */
interface WebsiteRegistryInterface
{
    /**
     * @return WebsiteInterface[]
     */
    public function all(): array;

    public function get(string $id): WebsiteInterface;
}
