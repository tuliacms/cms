<?php

declare(strict_types=1);

namespace Tulia\Cms\Website\Domain\WriteModel\Service\TranslationCopyMachine;

/**
 * @author Adam Banaszkiewicz
 */
final class TranslationsCopyMachineFactory
{
    /**
     * @param CopyBusInterface[] $buses
     */
    public function __construct(
        private readonly iterable $buses,
    ) {
    }

    public function create(
        string $websiteId,
        string $sourceLocale,
        ProgressStreamInterface $progressStream = null,
    ): TranslationsCopyMachineInterface {
        return new TranslationsCopyMachine(
            $this->buses,
            $websiteId,
            $sourceLocale,
            $progressStream ?? new VoidProgressStream(),
        );
    }
}
