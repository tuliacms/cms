<?php

declare(strict_types=1);

namespace Tulia\Cms\Options\Domain\WriteModel\Service;

/**
 * @author Adam Banaszkiewicz
 */
interface OptionsProviderInterface
{
    public function provide(): array;
}
