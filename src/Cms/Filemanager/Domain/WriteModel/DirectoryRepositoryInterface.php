<?php

declare(strict_types=1);

namespace Tulia\Cms\Filemanager\Domain\WriteModel;

use Tulia\Cms\Filemanager\Domain\WriteModel\Model\Directory;

/**
 * @author Adam Banaszkiewicz
 */
interface DirectoryRepositoryInterface
{
    public function getNextId(): string;
    public function get(string $id): Directory;
    public function getByFile(string $id): Directory;
    public function save(Directory $directory): void;
    public function delete(Directory $directory): void;
}
