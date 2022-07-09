<?php

declare(strict_types=1);

namespace Tulia\Cms\User\Infrastructure\Persistence\Dbal\WriteModel;

use Doctrine\DBAL\Connection;
use Tulia\Cms\Content\Attributes\Domain\WriteModel\AttributesRepositoryInterface;
use Tulia\Cms\Content\Type\Domain\ReadModel\Service\ContentTypeRegistryInterface;
use Tulia\Cms\Shared\Domain\WriteModel\UuidGeneratorInterface;
use Tulia\Cms\User\Domain\WriteModel\Model\AggregateId;
use Tulia\Cms\User\Domain\WriteModel\Model\User;
use Tulia\Cms\User\Domain\WriteModel\UserRepositoryInterface;

/**
 * @author Adam Banaszkiewicz
 */
class DbalUserRepository implements UserRepositoryInterface
{
    public function __construct(
        private Connection $connection,
        private DbalPersister $persister,
        private AttributesRepositoryInterface $attributeRepository,
        private UuidGeneratorInterface $uuidGenerator,
        private ContentTypeRegistryInterface $contentTypeRegistry,
    ) {
    }

    public function generateNextId(): AggregateId
    {
        return new AggregateId($this->uuidGenerator->generate());
    }

    public function find(string $id): ?User
    {
        $user = $this->connection->fetchAllAssociative('
            SELECT *
            FROM #__user AS tm
            WHERE tm.id = :id
            LIMIT 1', [
            'id' => $id,
        ]);

        if (empty($user)) {
            return null;
        }

        $contentType = $this->contentTypeRegistry->get('user');
        $attributes = $this->attributeRepository->findAll('user', $id, $contentType->buildAttributesMapping());

        $user = reset($user);
        $user['attributes'] =  $attributes;
        $user['roles'] = json_decode($user['roles'], true);

        return User::fromArray($user);
    }

    public function save(User $user): void
    {
        $data = $user->toArray();

        $this->connection->transactional(function () use ($data) {
            if ($this->recordExists($data['id'])) {
                $this->persister->update($data);
            } else {
                $this->persister->insert($data);
            }

            $this->attributeRepository->persist('user', $data['id'], $data['attributes']);
        });
    }

    public function delete(User $user): void
    {
        $data = $user->toArray();

        $this->connection->transactional(function () use ($data) {
            $this->persister->delete($data);
            $this->attributeRepository->delete('user', $data['id']);
        });
    }

    private function recordExists(string $id): bool
    {
        $result = $this->connection->fetchAllAssociative('SELECT id FROM #__user WHERE id = :id LIMIT 1', ['id' => $id]);

        return isset($result[0]['id']) && $result[0]['id'] === $id;
    }
}
