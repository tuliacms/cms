<?php

declare(strict_types=1);

namespace Tulia\Cms\Website\Infrastructure\Persistence\LocalStorage;

use Symfony\Component\Uid\Uuid;
use Tulia\Cms\Platform\Domain\Service\DynamicConfigurationInterface;
use Tulia\Cms\Shared\Infrastructure\Utils\Hydrator\HydratorInterface;
use Tulia\Cms\Website\Domain\WriteModel\Exception\WebsiteNotFoundException;
use Tulia\Cms\Website\Domain\WriteModel\Model\Website;
use Tulia\Cms\Website\Domain\WriteModel\WebsiteRepositoryInterface;

/**
 * @author Adam Banaszkiewicz
 */
final class LocalStorageWebsiteRepository implements WebsiteRepositoryInterface
{
    public function __construct(
        private readonly DynamicConfigurationInterface $configuration,
        private readonly HydratorInterface $hydrator,
    ) {
    }

    public function getNextId(): string
    {
        return (string) Uuid::v4();
    }

    public function get(string $id): Website
    {
        $website = $this->findWebsite($id);

        if (!$website) {
            throw new WebsiteNotFoundException(sprintf('Website %s not exists.', $id));
        }

        return $website;
    }

    public function save(Website $website): void
    {
        $websites = $this->configuration->get('cms.websites');
        $updated = false;

        foreach ($websites as $key => $val) {
            if ($val['id'] === $website->getId()) {
                $websites[$key] = $website->toArray();
                $updated = true;
            }
        }

        if (!$updated) {
            $websites[] = $website->toArray();
        }

        $this->configuration->set('cms.websites', $websites);
        $this->configuration->write();
    }

    public function delete(Website $website): void
    {
        $websites = $this->configuration->get('cms.websites');

        foreach ($websites as $key => $val) {
            if ($val['id'] === $website->getId()) {
                unset($websites[$key]);
            }
        }

        $this->configuration->set('cms.websites', $websites);
        $this->configuration->write();
    }

    private function findWebsite(string $id): ?Website
    {
        $websites = $this->configuration->get('cms.websites');

        foreach ($websites as $website) {
            if ($website['id'] === $id) {
                return Website::buildFromArray($website);
            }
        }

        return null;
    }
}
