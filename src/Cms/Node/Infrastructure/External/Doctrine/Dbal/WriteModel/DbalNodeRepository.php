<?php

declare(strict_types=1);

namespace Tulia\Cms\Node\Infrastructure\External\Doctrine\Dbal\WriteModel;

use Tulia\Cms\Content\Attributes\Domain\WriteModel\AttributesRepositoryInterface;
use Tulia\Cms\Content\Type\Domain\ReadModel\Service\ContentTypeRegistryInterface;
use Tulia\Cms\Content\Type\Domain\WriteModel\Exception\ContentTypeNotExistsException;
use Tulia\Cms\Node\Domain\WriteModel\Model\Node;
use Tulia\Cms\Node\Domain\WriteModel\Model\ValueObject\Author;
use Tulia\Cms\Node\Domain\WriteModel\Service\NodeRepositoryInterface;
use Tulia\Cms\Node\Domain\WriteModel\Service\NodeWriteStorageInterface;
use Tulia\Cms\Shared\Domain\WriteModel\UuidGeneratorInterface;

/**
 * @author Adam Banaszkiewicz
 */
final class DbalNodeRepository implements NodeRepositoryInterface
{
    private NodeWriteStorageInterface $storage;
    private AttributesRepositoryInterface $attributeRepository;
    private UuidGeneratorInterface $uuidGenerator;
    private ContentTypeRegistryInterface $contentTypeRegistry;

    public function __construct(
        NodeWriteStorageInterface $storage,
        AttributesRepositoryInterface $attributeRepository,
        UuidGeneratorInterface $uuidGenerator,
        ContentTypeRegistryInterface $contentTypeRegistry
    ) {
        $this->storage = $storage;
        $this->attributeRepository = $attributeRepository;
        $this->uuidGenerator = $uuidGenerator;
        $this->contentTypeRegistry = $contentTypeRegistry;
    }

    public function createNew(string $nodeType, string $author): Node
    {
        $this->contentTypeRegistry->get($nodeType);

        return Node::createNew(
            $this->uuidGenerator->generate(),
            $nodeType,
            $this->currentWebsite->getId(),
            $this->currentWebsite->getLocale()->getCode(),
            new Author($author)
        );
    }

    /**
     * @throws ContentTypeNotExistsException
     * @throws \Exception
     */
    public function find(string $id): ?Node
    {
        $node = $this->storage->find(
            $id,
            $this->currentWebsite->getId(),
            $this->currentWebsite->getLocale()->getCode(),
            $this->currentWebsite->getDefaultLocale()->getCode()
        );

        if (empty($node)) {
            return null;
        }

        $contentType = $this->contentTypeRegistry->get($node['type']);
        $node['attributes'] = $this->attributeRepository->findAll('node', $id, $contentType->buildAttributesMapping());

        $node = Node::fromArray($node);

        return $node;
    }

    public function insert(Node $node): void
    {
        $data = $node->toArray();

        $this->storage->insert($data, $this->currentWebsite->getDefaultLocale()->getCode());
        $this->attributeRepository->persist(
            'node',
            $node->getId(),
            $data['attributes']
        );
    }

    public function update(Node $node): void
    {
        $data = $node->toArray();

        $this->storage->update($data, $this->currentWebsite->getDefaultLocale()->getCode());
        $this->attributeRepository->persist(
            'node',
            $node->getId(),
            $data['attributes']
        );
    }

    public function delete(Node $node): void
    {
        $this->storage->delete($node->toArray());
        $this->attributeRepository->delete('node', $node->getId());
    }
}
