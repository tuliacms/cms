<?php

declare(strict_types=1);

namespace Tulia\Cms\User\Infrastructure\Cms\SearchAnything;

use Tulia\Cms\SearchAnything\Domain\WriteModel\Service\AbstractDocumentCollector;
use Tulia\Cms\User\Domain\ReadModel\Query\UserSearchCollectorInterface;
use Tulia\Cms\SearchAnything\Domain\WriteModel\Service\DocumentCollectorInterface;
use Tulia\Cms\SearchAnything\Domain\WriteModel\Service\IndexInterface;

/**
 * @author Adam Banaszkiewicz
 * @final
 * @lazy
 */
class UsersDocumentCollector extends AbstractDocumentCollector
{
    public function __construct(
        private readonly UserSearchCollectorInterface $collector
    ) {
    }

    public function collect(IndexInterface $index, string $websiteId, string $locale, int $offset, int $limit): void
    {
        foreach ($this->collector->collectDocuments($offset, $limit) as $user) {
            $document = $index->document($user['id']);

            if ($user['name']) {
                $document->setTitle($user['name'].' - '.$user['email']);
            } else {
                $document->setTitle($user['email']);
            }

            $document->setLink('backend.users.edit', ['id' => $user['id']]);

            $index->save($document);
        }
    }

    public function countDocuments(string $websiteId, string $locale): int
    {
        return $this->collector->countDocuments();
    }

    public function getIndex(): string
    {
        return 'user';
    }

    public function isMultilingual(): bool
    {
        return false;
    }
}
