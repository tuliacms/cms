<?php

declare(strict_types=1);

namespace Tulia\Cms\Website\Domain\WriteModel\Service\TranslationCopyMachine;

/**
 * @author Adam Banaszkiewicz
 */
interface CopyBusInterface
{
    public function count(string $websiteId, string $from): int;

    public function copy(string $websiteId, string $from, string $to, int $offset, int $limit): int;

    public function delete(string $websiteId, string $from, int $offset, int $limit): int;
}
