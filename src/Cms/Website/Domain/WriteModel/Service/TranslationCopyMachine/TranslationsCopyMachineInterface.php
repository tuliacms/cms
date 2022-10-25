<?php

declare(strict_types=1);

namespace Tulia\Cms\Website\Domain\WriteModel\Service\TranslationCopyMachine;

/**
 * @author Adam Banaszkiewicz
 */
interface TranslationsCopyMachineInterface
{
    public function count(): int;

    public function copyTo(string $locale): int;

    public function deleteFrom(string $locale): int;
}
