<?php

declare(strict_types=1);

namespace Tulia\Cms\Website\Infrastructure\Persistence\Doctrine\Orm;

use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\Uid\Uuid;
use Tulia\Cms\Website\Domain\WriteModel\Exception\WebsiteNotFoundException;
use Tulia\Cms\Website\Domain\WriteModel\Model\Website;
use Tulia\Cms\Website\Domain\WriteModel\WebsiteRepositoryInterface;

/**
 * @author Adam Banaszkiewicz
 */
final class DoctrineWebsiteRepository extends ServiceEntityRepository implements WebsiteRepositoryInterface
{
    public function __construct(
        ManagerRegistry $registry
    ) {
        parent::__construct($registry, Website::class);
    }

    public function getNextId(): string
    {
        return (string) Uuid::v4();
    }

    public function get(string $id): Website
    {
        $website = $this->find($id);

        if (!$website) {
            throw new WebsiteNotFoundException(sprintf('Website %s not exists.', $id));
        }

        return $website;
    }

    public function save(Website $website): void
    {
        $this->_em->persist($website);
        $this->_em->flush();
    }

    public function delete(Website $website): void
    {
        $this->_em->remove($website);
        $this->_em->flush();
    }
}
