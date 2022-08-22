<?php

declare(strict_types=1);

namespace Tulia\Cms\Options\Infrastructure\Persistence\Doctrine\Orm;

use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Tulia\Cms\Options\Domain\WriteModel\Exception\OptionNotFoundException;
use Tulia\Cms\Options\Domain\WriteModel\Model\Option;
use Tulia\Cms\Options\Domain\WriteModel\OptionsRepositoryInterface;

/**
 * @author Adam Banaszkiewicz
 */
final class DoctrineOptionsRepository extends ServiceEntityRepository implements OptionsRepositoryInterface
{
    public function __construct(
        ManagerRegistry $registry,
    ) {
        parent::__construct($registry, Option::class);
    }

    public function get(string $name, string $websiteId): Option
    {
        $option = $this->findOneBy(['name' => $name, 'websiteId' => $websiteId]);

        if (!$option) {
            throw OptionNotFoundException::fromName($name);
        }

        return $option;
    }

    public function getAllForWebsite(string $websiteId): array
    {
        return $this->findBy(['websiteId' => $websiteId]);
    }

    public function save(Option $option): void
    {
        $this->_em->persist($option);
        $this->_em->flush();
    }

    public function delete(Option $option): void
    {
        $this->_em->remove($option);
        $this->_em->flush();
    }
}
