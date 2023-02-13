<?php

declare(strict_types=1);

namespace Tulia\Cms\Node\Infrastructure\Persistence\Doctrine\Dbal\WriteModel;

use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Tulia\Cms\Content\Type\Domain\ReadModel\Service\ContentTypeRegistryInterface;
use Tulia\Cms\Node\Domain\WriteModel\Exception\NodeDoesntExistsException;
use Tulia\Cms\Node\Domain\WriteModel\Model\Node;
use Tulia\Cms\Node\Domain\WriteModel\Service\NodeRepositoryInterface;
use Tulia\Cms\Shared\Domain\WriteModel\UuidGeneratorInterface;

/**
 * @author Adam Banaszkiewicz
 */
final class DbalNodeRepository extends ServiceEntityRepository implements NodeRepositoryInterface
{
    public function __construct(
        ManagerRegistry $registry,
        private UuidGeneratorInterface $uuidGenerator,
        private ContentTypeRegistryInterface $contentTypeRegistry,
    ) {
        parent::__construct($registry, Node::class);
    }

    public function getNextId(): string
    {
        return $this->uuidGenerator->generate();
    }

    public function referenceTo(string $id): Node
    {
        return $this->_em->getReference(Node::class, $id);
    }

    public function get(string $id): Node
    {
        $node = $this->find($id);


        if (!$node) {
            throw NodeDoesntExistsException::fromId($id);
        }

        return $node;
    }

    public function save(Node $node): void
    {
        $this->_em->persist($node);
        $this->_em->flush();
    }

    public function delete(Node $node): void
    {
        $this->_em->remove($node);
        $this->_em->flush();
    }
}
