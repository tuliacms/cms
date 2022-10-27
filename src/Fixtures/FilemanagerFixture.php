<?php

declare(strict_types=1);

namespace Tulia\Cms\Fixtures;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Bundle\FixturesBundle\FixtureGroupInterface;
use Doctrine\Persistence\ObjectManager;
use Tulia\Cms\Filemanager\Domain\WriteModel\Model\Directory;

/**
 * @author Adam Banaszkiewicz
 */
final class FilemanagerFixture extends Fixture implements FixtureGroupInterface
{
    public function load(ObjectManager $manager)
    {
        $rootDirectory = Directory::root();

        $manager->persist($rootDirectory);
        $manager->flush();
    }

    public static function getGroups(): array
    {
        return ['local-database', 'setup'];
    }
}
