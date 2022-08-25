<?php

declare(strict_types=1);

namespace Tulia\Cms\Filemanager\Domain\WriteModel\Model;

use Tulia\Cms\Shared\Domain\WriteModel\Model\ValueObject\ImmutableDateTime;

/**
 * @author Adam Banaszkiewicz
 * @final
 */
class File
{
    public function __construct(
        private string $id,
        private ?Directory $directory,
        private string $filename,
        private string $extension,
        private string $type,
        private int $size,
        private string $path,
        private ImmutableDateTime $createdAt,
        private ImmutableDateTime $updatedAt,
    ) {
        $this->createdAt = ImmutableDateTime::now();
    }

    public function getId(): string
    {
        return $this->id;
    }

    public function getFilepath(): string
    {
        return sprintf('%s/%s', $this->path, $this->filename);
    }
}
