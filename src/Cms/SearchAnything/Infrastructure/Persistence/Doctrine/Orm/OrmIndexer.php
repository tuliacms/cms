<?php

declare(strict_types=1);

namespace Tulia\Cms\SearchAnything\Infrastructure\Persistence\Doctrine\Orm;

use Doctrine\DBAL\Connection;
use Tulia\Cms\SearchAnything\Domain\WriteModel\Service\IndexerInterface;
use Tulia\Cms\SearchAnything\Domain\WriteModel\Service\IndexInterface;

/**
 * @author Adam Banaszkiewicz
 */
final class OrmIndexer implements IndexerInterface
{
    private array $indexes = [];

    public function __construct(
        private DocumentRepository $repository,
        private Connection $connection,
    ) {
    }

    public function index(string $index, string $locale): IndexInterface
    {
        if (!isset($this->indexes[$locale][$index])) {
            $this->indexes[$locale][$index] = new OrmIndex($this->repository, $this->connection, $index, $locale);
        }

        return $this->indexes[$locale][$index];
    }
}
