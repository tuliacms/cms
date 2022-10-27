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
        return $this->process('copy', $locale);
    }

    public function deleteFrom(string $locale): int
    {
        return $this->process('delete', $locale);
    }

    private function process(string $action, string $locale): int
    {
        $total = $this->count();
        $this->progressStream->start($total);

        $processedRows = 0;

        foreach ($this->buses as $bus) {
            $busTotal = $this->busesCountCache[get_class($bus)];
            $limit = 10;
            $iterations = ceil($busTotal / $limit);

            for ($i = 0; $i < $iterations; $i++) {
                $done = $bus->{$action}(
                    $this->websiteId,
                    $this->sourceLocale,
                    $locale,
                    offset: $i * $limit,
                    limit: $limit,
                );

                $this->progressStream->advance($done);
                $processedRows += $done;
            }
        }

        $this->progressStream->end();

        return $processedRows;
    }
}
