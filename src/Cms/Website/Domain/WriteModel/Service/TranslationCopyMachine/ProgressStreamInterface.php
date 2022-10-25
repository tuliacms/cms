<?php

declare(strict_types=1);

namespace Tulia\Cms\Website\Domain\WriteModel\Service\TranslationCopyMachine;

/**
 * @author Adam Banaszkiewicz
 */
interface ProgressStreamInterface
{
    public function start(int $total): void;

    public function advance(int $done): void;

    public function end(): void;
}
