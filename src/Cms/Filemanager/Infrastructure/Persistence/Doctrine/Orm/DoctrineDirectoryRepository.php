<?php

declare(strict_types=1);

namespace Tulia\Cms\Filemanager\Infrastructure\Persistence\Doctrine\Orm;

use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\Uid\Uuid;
use Tulia\Cms\Filemanager\Domain\WriteModel\DirectoryRepositoryInterface;
use Tulia\Cms\Filemanager\Domain\WriteModel\Exception\DirectoryDoesntExistsException;
use Tulia\Cms\Filemanager\Domain\WriteModel\Model\Directory;

/**
 * @author Adam Banaszkiewicz
 */
final class DoctrineDirectoryRepository extends ServiceEntityRepository implements DirectoryRepositoryInterface
{
    public function __construct(
        ManagerRegistry $registry,
    ) {
        parent::__construct($registry, Directory::class);
    }

    public function getNextId(): string
    {
        return (string) Uuid::v4();
    }

    public function get(string $id): Directory
    {
        $directory = $this->find($id);

        if (!$directory) {
            throw DirectoryDoesntExistsException::fromId($id);
        }

        return $directory;
    }

    public function getByFile(string $id): Directory
    {
        $directoryId = (string) $this->_em->getConnection()->fetchOne('SELECT BIN_TO_UUID(directory_id) AS directory_id FROM #__filemanager_file WHERE id = :id LIMIT 1', [
            'id' => Uuid::fromString($id)->toBinary(),
        ]);

        return $this->get($directoryId);
    }

    public function save(Directory $directory): void
    {
        $this->_em->persist($directory);
        $this->_em->flush();
    }

    public function delete(Directory $directory): void
    {
        $this->_em->remove($directory);
        $this->_em->flush();
    }
}
