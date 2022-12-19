<?php

declare(strict_types=1);

namespace Tulia\Cms\Taxonomy\Infrastructure\Persistence\Doctrine\Orm;

use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\Uid\Uuid;
use Tulia\Cms\Content\Type\Domain\ReadModel\Service\ContentTypeRegistryInterface;
use Tulia\Cms\Taxonomy\Domain\WriteModel\Model\Taxonomy;
use Tulia\Cms\Taxonomy\Domain\WriteModel\Service\TaxonomyRepositoryInterface;

/**
 * @author Adam Banaszkiewicz
 */
class DoctrineTaxonomyRepository extends ServiceEntityRepository implements TaxonomyRepositoryInterface
{
    public function __construct(
        ManagerRegistry $registry,
        private readonly ContentTypeRegistryInterface $contentTypeRegistry,
    ) {
        parent::__construct($registry, Taxonomy::class);
    }

    public function get(string $type, string $websiteId, array $locales, string $locale): Taxonomy
    {
        $taxonomy = $this->findOneByType($type);

        if (!$taxonomy) {
            $taxonomy = Taxonomy::create($type, $websiteId, $locales, $locale);
        }

        return $taxonomy;
    }

    public function save(Taxonomy $taxonomy): void
    {
        $this->_em->persist($taxonomy);
        $this->_em->flush();
    }

    public function delete(Taxonomy $taxonomy): void
    {
        //taxonomy->delete();
        $this->_em->remove($taxonomy);
        $this->_em->flush();
    }

    public function getNextId(): string
    {
        return (string) Uuid::v4();
    }
}
