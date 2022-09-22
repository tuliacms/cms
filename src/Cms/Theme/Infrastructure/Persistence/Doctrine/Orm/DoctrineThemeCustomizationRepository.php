<?php

declare(strict_types=1);

namespace Tulia\Cms\Theme\Infrastructure\Persistence\Doctrine\Orm;

use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Tulia\Cms\Theme\Domain\WriteModel\Model\ThemeCustomization;
use Tulia\Cms\Theme\Domain\WriteModel\ThemeCustomizationRepositoryInterface;

/**
 * @author Adam Banaszkiewicz
 */
final class DoctrineThemeCustomizationRepository extends ServiceEntityRepository implements ThemeCustomizationRepositoryInterface
{
    public function __construct(
        ManagerRegistry $registry,
    ) {
        parent::__construct($registry, ThemeCustomization::class);
    }

    public function get(string $theme, string $websiteId): ThemeCustomization
    {
        $customization = $this->findOneBy(['websiteId' => $websiteId, 'theme' => $theme]);

        if (!$customization) {
            $customization = ThemeCustomization::create($theme, $websiteId);
        }

        return $customization;
    }

    public function save(ThemeCustomization $customization): void
    {
        $this->_em->persist($customization);
        $this->_em->flush();
    }
}
