<?php

declare(strict_types=1);

namespace Tulia\Cms\Website\Domain\WriteModel\Service\TranslationCopyMachine;

/**
 * @author Adam Banaszkiewicz
 */
final class TranslationsCopyMachine implements TranslationsCopyMachineInterface
{
    private array $busesCountCache = [];

    /**
     * @param CopyBusInterface[] $buses
     */
    public function __construct(
        private readonly iterable $buses,
        private readonly string $websiteId,
        private readonly string $sourceLocale,
        private readonly ProgressStreamInterface $progressStream,
    ) {
    }

    public function count(): int
    {
        if ([] !== $this->busesCountCache) {
            return array_sum($this->busesCountCache);
        }

        $rowsToCopy = 0;

        foreach ($this->buses as $bus) {
            $count = $bus->count($this->websiteId, $this->sourceLocale);
            $this->busesCountCache[get_class($bus)] = $count;
            $rowsToCopy += $count;
        }

        return $rowsToCopy;
    }

    public function copyTo(string $locale): int
    {
        $total = $this->count();
        $this->progressStream->start($total);

        $copiedRows = 0;

        foreach ($this->buses as $bus) {
            $busTotal = $this->busesCountCache[get_class($bus)];
            $limit = 10;
            $iterations = ceil($busTotal / $limit);

            for ($i = 0; $i < $iterations; $i++) {
                $done = $bus->copy(
                    $this->websiteId,
                    $this->sourceLocale,
                    $locale,
                    offset: $i * $limit,
                    limit: $limit,
                );

                $this->progressStream->advance($done);
                $copiedRows += $done;
            }
        }

        $this->progressStream->end();

        return $copiedRows;
    }

    public function deleteFrom(string $locale): int
    {
        $total = $this->count();
        $this->progressStream->start($total);

        $deletedRows = 0;

        foreach ($this->buses as $bus) {
            $busTotal = $this->busesCountCache[get_class($bus)];
            $limit = 10;
            $iterations = ceil($busTotal / $limit);

            for ($i = 0; $i < $iterations; $i++) {
                $done = $bus->delete(
                    $this->websiteId,
                    $locale,
                    offset: $i * $limit,
                    limit: $limit,
                );

                $this->progressStream->advance($done);
                $deletedRows += $done;
            }
        }

        $this->progressStream->end();

        return $deletedRows;
    }
}
