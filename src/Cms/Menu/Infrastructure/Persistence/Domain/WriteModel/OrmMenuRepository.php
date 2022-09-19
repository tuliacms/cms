<?php

declare(strict_types=1);

namespace Tulia\Cms\Menu\Infrastructure\Persistence\Domain\WriteModel;

use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\Uid\Uuid;
use Tulia\Cms\Menu\Domain\WriteModel\Exception\MenuNotExistsException;
use Tulia\Cms\Menu\Domain\WriteModel\MenuRepositoryInterface;
use Tulia\Cms\Menu\Domain\WriteModel\Model\Menu;

/**
 * @author Adam Banaszkiewicz
 */
class OrmMenuRepository extends ServiceEntityRepository implements MenuRepositoryInterface
{
    public function __construct(
        ManagerRegistry $registry
    ) {
        parent::__construct($registry, Menu::class);
    }

    public function getNextId(): string
    {
        return (string) Uuid::v4();
    }

    public function get(string $id): Menu
    {
        $menu = $this->find($id);

        if (!$menu) {
            throw MenuNotExistsException::fromId($id);
        }

        return $menu;
    }

    public function save(Menu $menu): void
    {
        $this->_em->persist($menu);
        $this->_em->flush();
    }

    public function delete(Menu $menu): void
    {
        $menu->delete();
        $this->_em->remove($menu);
        $this->_em->flush();
    }
}
