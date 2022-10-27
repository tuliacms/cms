<?php

declare(strict_types=1);

namespace Tulia\Cms\Widget\Infrastructure\Persistence\Doctrine\Orm;

use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\Uid\Uuid;
use Tulia\Cms\Widget\Domain\WriteModel\Exception\WidgetNotFoundException;
use Tulia\Cms\Widget\Domain\WriteModel\Model\Widget;
use Tulia\Cms\Widget\Domain\WriteModel\WidgetRepositoryInterface;

/**
 * @author Adam Banaszkiewicz
 */
final class DoctrineWidgetRepository extends ServiceEntityRepository implements WidgetRepositoryInterface
{
    public function __construct(
        ManagerRegistry $registry,
    ) {
        parent::__construct($registry, Widget::class);
    }

    public function getNextId(): string
    {
        return (string) Uuid::v4();
    }

    public function get(string $id): Widget
    {
        $widget = $this->find($id);

        if (!$widget) {
            throw new WidgetNotFoundException(sprintf('Widget %s not exists.', $id));
        }

        return $widget;
    }

    public function save(Widget $widget)
    {
        $this->_em->persist($widget);
        $this->_em->flush();
    }

    public function delete(Widget $widget): void
    {
        $widget->delete();

        $this->_em->remove($widget);
        $this->_em->flush();
    }
}
