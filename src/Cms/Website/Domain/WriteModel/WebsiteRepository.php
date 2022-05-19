<?php

declare(strict_types=1);

namespace Tulia\Cms\Website\Domain\WriteModel;

use Tulia\Cms\Shared\Domain\WriteModel\UuidGeneratorInterface;
use Tulia\Cms\Shared\Infrastructure\Bus\Event\EventBusInterface;
use Tulia\Cms\Website\Domain\WriteModel\Event\WebsiteCreated;
use Tulia\Cms\Website\Domain\WriteModel\Event\WebsiteDeleted;
use Tulia\Cms\Website\Domain\WriteModel\Event\WebsiteUpdated;
use Tulia\Cms\Website\Domain\WriteModel\Exception\WebsiteNotFoundException;
use Tulia\Cms\Website\Domain\WriteModel\Model\Locale;
use Tulia\Cms\Website\Domain\WriteModel\Model\Website;

/**
 * @author Adam Banaszkiewicz
 */
class WebsiteRepository implements WebsiteRepositoryInterface
{
    private WebsiteStorageInterface $storage;
    private UuidGeneratorInterface $uuidGenerator;
    private EventBusInterface $eventBus;

    public function __construct(
        WebsiteStorageInterface $storage,
        UuidGeneratorInterface $uuidGenerator,
        EventBusInterface $eventBus
    ) {
        $this->storage = $storage;
        $this->uuidGenerator = $uuidGenerator;
        $this->eventBus = $eventBus;
    }

    public function createNew(array $data = []): Website
    {
        return Website::buildFromArray(array_merge($data, [
            'id' => $this->uuidGenerator->generate(),
        ]));
    }

    /**
     * {@inheritdoc}
     */
    public function find(string $id): Website
    {
        $website = $this->storage->find($id);

        if ($website === null) {
            throw new WebsiteNotFoundException(sprintf('Website %s not exists.', $id));
        }

        $website['locales'] = array_map([$this, 'hydrateLocale'], $website['locales']);

        return $this->hydrateWebsite($website);
    }

    /**
     * {@inheritdoc}
     */
    public function create(Website $website): void
    {
        $this->storage->insert($this->extract($website));
        $this->eventBus->dispatch(new WebsiteCreated($website->getId()->getValue()));
    }

    /**
     * {@inheritdoc}
     */
    public function update(Website $website): void
    {
        $this->storage->update($this->extract($website));
        $this->eventBus->dispatch(new WebsiteUpdated($website->getId()->getValue()));
    }

    /**
     * {@inheritdoc}
     */
    public function delete(string $id): void
    {
        $this->storage->delete($id);
        $this->eventBus->dispatch(new WebsiteDeleted($id));
    }

    private function extract(Website $website): array
    {
        $data = [];
        $data['id'] = $website->getId()->getValue();
        $data['backend_prefix'] = '/administrator';
        $data['active'] = $website->isActive();
        $data['name'] = $website->getName();
        $data['locales'] = [];

        foreach ($website->getLocales() as $locale) {
            $data['locales'][] = [
                'code' => $locale->getCode(),
                'domain' => $locale->getDomain(),
                'domain_development' => $locale->getDomainDevelopment(),
                'locale_prefix' => $locale->getLocalePrefix(),
                'path_prefix' => $locale->getPathPrefix(),
                'ssl_mode' => $locale->getSslMode(),
                'is_default' => $locale->isDefault(),
            ];
        }

        return $data;
    }

    private function hydrateLocale(array $data): Locale
    {
        return new Locale(
            $data['code'],
            $data['domain'],
            $data['domain_development'],
            $data['locale_prefix'],
            $data['path_prefix'],
            $data['ssl_mode'],
            (bool) $data['is_default'],
        );
    }

    private function hydrateWebsite(array $data): Website
    {
        return new Website(
            $data['id'],
            $data['name'],
            $data['backend_prefix'],
            $data['locales'],
            (bool) $data['active']
        );
    }
}
