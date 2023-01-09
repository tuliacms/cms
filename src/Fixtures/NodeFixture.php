<?php

declare(strict_types=1);

namespace Tulia\Cms\Fixtures;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Bundle\FixturesBundle\FixtureGroupInterface;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;
use Tulia\Cms\Options\Domain\WriteModel\Model\Option;
use Tulia\Cms\Website\Domain\WriteModel\Model\Website;

/**
 * @author Adam Banaszkiewicz
 */
final class NodeFixture extends Fixture implements FixtureGroupInterface, DependentFixtureInterface
{
    public function load(ObjectManager $manager): void
    {
        /** @var Website $website */
        $website = $this->getReference(ReferencesEnum::WEBSITE);

        $categorySetting = new Option('node.page.category_taxonomy', 'category', $website->getId(), false, true);

        $manager->persist($categorySetting);
        $manager->flush();
    }

    public static function getGroups(): array
    {
        return ['local-database'];
    }

    public function getDependencies(): array
    {
        return [
            WebsiteFixture::class,
        ];
    }
}
