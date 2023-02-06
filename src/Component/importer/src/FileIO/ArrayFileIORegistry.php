<?php

declare(strict_types=1);

namespace Tulia\Component\Importer\FileIO;

use Tulia\Component\Importer\Exception\FileNotSupportedException;

/**
 * @author Adam Banaszkiewicz
 */
class ArrayFileIORegistry implements FileIORegistryInterface
{
    /** @var FileIOInterface[] */
    private array $readers = [];

    public function addReader(FileIOInterface $reader)
    {
        $this->readers[] = $reader;
    }

    public function all(): array
    {
        return $this->readers;
    }

    public function getSupported(string $filepath): FileIOInterface
    {
        foreach ($this->readers as $reader) {
            if ($reader->supports($filepath)) {
                return $reader;
            }
        }

        throw FileNotSupportedException::fromFilepath($filepath);
    }
}
