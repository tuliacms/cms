<?php

declare(strict_types=1);

namespace Tulia\Cms\SearchAnything\Infrastructure\Persistence\Doctrine\Orm;

use Doctrine\DBAL\Connection;
use Tulia\Cms\SearchAnything\Domain\WriteModel\Model\Document;
use Tulia\Cms\SearchAnything\Domain\WriteModel\Service\IndexInterface;

/**
 * @author Adam Banaszkiewicz
 */
final class OrmIndex implements IndexInterface
{
    private int $delta = 0;

    public function __construct(
        private DocumentRepository $repository,
        private Connection $connection,
        private string $index,
        private string $locale,
    ) {
    }

    public function document(string $sourceId): Document
    {
        $document = $this->repository->findOneBy([
            'sourceId' => $sourceId,
            'indexGroup' => $this->index,
            'locale' => $this->locale
        ]);

        return $document ?? new Document($this->index, $sourceId, $this->locale);
    }

    public function save(Document $document): void
    {
        $this->delta++;
        $this->repository->save($document);
    }

    public function delete(Document $document): void
    {
        $this->delta--;
        $this->repository->delete($document);
    }

    public function clear(): void
    {
        $this->connection->createQueryBuilder()
            ->delete('#__search_anything_document')
            ->where('locale = :locale')
            ->andWhere('index_group = :index')
            ->setParameter('locale', $this->locale)
            ->setParameter('index', $this->index)
            ->executeQuery()
        ;
    }

    public function getDelta(): int
    {
        return $this->delta;
    }
}
