<?php

declare(strict_types=1);

namespace Tulia\Cms\Node\Domain\WriteModel\Service;

/**
 * @author Adam Banaszkiewicz
 */
interface ShortcodeProcessorInterface
{
    public function process(string $input): string;
}
