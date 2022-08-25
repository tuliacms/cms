<?php

declare(strict_types=1);

namespace Tulia\Cms\Filemanager\Domain\WriteModel\Model;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Tulia\Cms\Filemanager\Domain\WriteModel\Service\FileStorageInterface;
use Tulia\Cms\Shared\Domain\WriteModel\Model\AbstractAggregateRoot;
use Tulia\Cms\Shared\Domain\WriteModel\Model\ValueObject\ImmutableDateTime;

/**
 * @author Adam Banaszkiewicz
 * @final
 */
class Directory extends AbstractAggregateRoot
{
    public const ROOT = '00000000-0000-0000-0000-000000000000';

    /** @var ArrayCollection<int, File> */
    private Collection $files;
    private int $level = 0;

    private function __construct(
        private string $id,
        private string $name,
        private ?Directory $parent = null
    ) {
        $this->files = new ArrayCollection();

        if ($this->parent) {
            $this->level = $this->parent->level + 1;
        }
    }

    public static function create(string $id, string $name, ?Directory $parent = null): self
    {
        return new self($id, $name, $parent);
    }

    public static function root(): self
    {
        return new self(self::ROOT, 'root', null);
    }

    public function placeFile(
        FileStorageInterface $fileStorage,
        string $id,
        string $filepath,
        ?string $filename = null
    ): void {
        $destination = '/uploads/' . date('Y/m');
        $file = $fileStorage->store($filepath, $destination, $filename);

        $this->files->add(new File(
            $id,
            $this,
            $file->getBasename(),
            $file->getExtension(),
            $this->guessType($file->getExtension()),
            $file->getSize(),
            $this->getPath($file, $destination),
            ImmutableDateTime::now(),
            ImmutableDateTime::now()
        ));
    }

    public function deleteFile(
        FileStorageInterface $fileStorage,
        string $id
    ) {
        foreach ($this->files as $file) {
            if ($file->getId() === $id) {
                $this->files->removeElement($file);
                $fileStorage->remove($file->getFilepath());
            }
        }
    }

    private function guessType(string $extension): string
    {
        switch ($extension) {
            case 'png':
            case 'jpg':
            case 'jpeg': return FileTypeEnum::IMAGE;
            case 'zip':
            case 'rar':
            case 'gz':
            case 'tar': return FileTypeEnum::ARCHIVE;
            case 'txt':
            case 'doc':
            case 'docx': return FileTypeEnum::DOCUMENT;
            case 'pdf': return FileTypeEnum::PDF;
            case 'svg': return FileTypeEnum::SVG;
        }

        return FileTypeEnum::FILE;
    }

    private function getPath(\SplFileInfo $file, string $destination): string
    {
        [, $filepath] = explode($destination, $file->getPath());

        return $destination.$filepath;
    }
}
