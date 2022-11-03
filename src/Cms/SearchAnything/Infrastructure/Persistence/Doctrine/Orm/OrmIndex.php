<?php

declare(strict_types=1);

namespace Tulia\Cms\SearchAnything\Infrastructure\Persistence\Doctrine\Orm;

use Doctrine\DBAL\Connection;
use Tulia\Cms\SearchAnything\Domain\WriteModel\Enum\LocalizationStrategyEnum;
use Tulia\Cms\SearchAnything\Domain\WriteModel\Enum\MultisiteStrategyEnum;
use Tulia\Cms\SearchAnything\Domain\WriteModel\Model\Document;
use Tulia\Cms\SearchAnything\Domain\WriteModel\Service\DocumentCollectorInterface;
use Tulia\Cms\SearchAnything\Domain\WriteModel\Service\IndexInterface;

/**
 * @author Adam Banaszkiewicz
 */
final class OrmIndex implements IndexInterface
{
    private int $delta = 0;

    public function __construct(
        private readonly DocumentRepository $repository,
        private readonly Connection $connection,
        private readonly string $name,
        private readonly string $localizationStrategy,
        private readonly string $multisiteStrategy,
        private readonly DocumentCollectorInterface $documentCollector,
    ) {
    }

    public function document(string $sourceId, string $webisteId = null, string $locale = null): Document
    {
        if ($this->localizationStrategy !== LocalizationStrategyEnum::UNILINGUAL->value && empty($locale)) {
            throw new \InvalidArgumentException('A locale parameter is required for this index.');
        }

        $document = $this->repository->findOneBy([
            'sourceId' => $sourceId,
            'indexGroup' => $this->name,
            'locale' => $locale,
            'websiteId' => $webisteId,
        ]);

        if (!$document) {
            $document = new Document(
                $this->name,
                $sourceId,
                $this->localizationStrategy,
                $this->multisiteStrategy,
                $webisteId,
                $locale,
            );
        }

        return $document;
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

    public function clear(?string $webisteId = null, ?string $locale = null): void
    {
        $this->connection->createQueryBuilder()
            ->delete('#__search_anything_document')
            ->andWhere('index_group = :index')
            ->andWhere('website_id = :website_id')
            ->andWhere('locale = :locale')
            ->setParameter('website_id', $webisteId)
            ->setParameter('locale', $locale)
            ->setParameter('index', $this->name)
            ->executeQuery()
        ;
    }

    public function getDelta(): int
    {
        return $this->delta;
    }

    public function getCollector(): DocumentCollectorInterface
    {
        return $this->documentCollector;
    }

    public function isMultisiteStrategy(MultisiteStrategyEnum $type): bool
    {
        return $this->multisiteStrategy === $type->value;
    }

    public function isLocalizationStrategy(LocalizationStrategyEnum $type): bool
    {
        return $this->localizationStrategy === $type->value;
    }

    public function getName(): string
    {
        return $this->name;
    }
}
