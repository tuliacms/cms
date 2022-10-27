<?php

declare(strict_types=1);

namespace Tulia\Cms\Website\Domain\WriteModel\Service\TranslationCopyMachine;

/**
 * @author Adam Banaszkiewicz
 */
interface CopyBusInterface
{
    public function count(string $websiteId, string $defaultLocale): int;

    public function copy(string $websiteId, string $defaultLocale, string $targetLocale, int $offset, int $limit): int;

    public function delete(string $websiteId, string $defaultLocale, string $targetLocale, int $offset, int $limit): int;
}
