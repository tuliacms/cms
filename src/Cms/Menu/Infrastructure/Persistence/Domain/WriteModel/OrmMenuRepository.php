<?php

declare(strict_types=1);

namespace Tulia\Cms\Menu\Infrastructure\Persistence\Domain\WriteModel;

use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\Uid\Uuid;
use Tulia\Cms\Content\Attributes\Domain\WriteModel\AttributesRepositoryInterface;
use Tulia\Cms\Menu\Domain\WriteModel\Exception\MenuNotExistsException;
use Tulia\Cms\Menu\Domain\WriteModel\MenuRepositoryInterface;
use Tulia\Cms\Menu\Domain\WriteModel\MewModel\Menu as NewMenu;
use Tulia\Cms\Menu\Domain\WriteModel\Model\Item;
use Tulia\Cms\Menu\Domain\WriteModel\Model\Menu;

/**
 * @author Adam Banaszkiewicz
 */
class DbalMenuRepository extends ServiceEntityRepository implements MenuRepositoryInterface
{
    public function __construct(
        ManagerRegistry $registry,
        private DbalMenuStorage $storage,
        private AttributesRepositoryInterface $attributesRepository,
    ) {
        parent::__construct($registry, NewMenu::class);
    }

    public function getNextId(): string
    {
        return (string) Uuid::v4();
    }

    public function createNewMenu(string $name): NewMenu
    {
        return NewMenu::create($this->getNextId(), $name);
    }

    public function createNewItem(Menu $menu): Item
    {
        return $menu->createNewItem();
    }

    public function get(string $id): NewMenu
    {
        $menu = $this->find($id);

        if (!$menu) {
            throw MenuNotExistsException::fromId($id);
        }

        return $menu;
    }

    public function save(NewMenu $menu): void
    {
        $this->_em->persist($menu);
        $this->_em->flush();
    }

    public function delete(NewMenu $menu): void
    {
        $menu->delete();
        $this->_em->remove($menu);
        $this->_em->flush();
    }
}
