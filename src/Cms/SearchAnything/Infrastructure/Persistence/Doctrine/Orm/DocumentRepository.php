<?php

declare(strict_types=1);

namespace Tulia\Cms\SearchAnything\Infrastructure\Persistence\Doctrine\Orm;

use Doctrine\Persistence\ManagerRegistry;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Tulia\Cms\SearchAnything\Domain\WriteModel\Model\Document;

/**
 * @author Adam Banaszkiewicz
 */
final class DocumentRepository extends ServiceEntityRepository
{
    public function __construct(
        ManagerRegistry $registry,
    ) {
        parent::__construct($registry, Document::class);
    }

    public function save(Document $document): void
    {
        $this->_em->persist($document);
        $this->_em->flush();
    }

    public function delete(Document $document): void
    {
        $this->_em->remove($document);
        $this->_em->flush();
    }
}
