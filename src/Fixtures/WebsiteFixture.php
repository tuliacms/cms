<?php

declare(strict_types=1);

namespace Tulia\Cms\Fixtures;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Bundle\FixturesBundle\FixtureGroupInterface;
use Doctrine\Persistence\ObjectManager;
use Tulia\Cms\Shared\Infrastructure\Bus\Event\EventBusInterface;
use Tulia\Cms\Website\Domain\WriteModel\Model\Website;
use Tulia\Cms\Website\Domain\WriteModel\Rules\CanAddLocale\CanAddLocale;
use Tulia\Cms\Website\Domain\WriteModel\WebsiteRepositoryInterface;

/**
 * @author Adam Banaszkiewicz
 */
final class WebsiteFixture extends Fixture implements FixtureGroupInterface
{
    public function __construct(
        private readonly WebsiteRepositoryInterface $repository,
        private readonly EventBusInterface $eventBus,
    ) {
    }

    public function load(ObjectManager $manager): void
    {
        $website = Website::create(
            $this->repository->getNextId(),
            'Default website',
            'en_US',
            'localhost',
            domainDevelopment: 'localhost',
        );
        $website->addLocale(new CanAddLocale(), 'pl_PL', localePrefix: '/pl');

        $manager->persist($website);
        $manager->flush();

        $this->addReference(ReferencesEnum::WEBSITE, $website);

        $this->eventBus->dispatchCollection($website->collectDomainEvents());
    }

    public static function getGroups(): array
    {
        return ['local-database'];
    }
}
