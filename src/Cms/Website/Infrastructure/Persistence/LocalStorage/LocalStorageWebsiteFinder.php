<?php

declare(strict_types=1);

namespace Tulia\Cms\Website\Infrastructure\Persistence\LocalStorage;

use Doctrine\DBAL\Connection;
use Symfony\Component\Uid\Uuid;
use Tulia\Cms\Platform\Domain\Service\DynamicConfigurationInterface;
use Tulia\Cms\Shared\Domain\ReadModel\Finder\Model\Collection;
use Tulia\Cms\Website\Domain\ReadModel\Finder\WebsiteFinderInterface;
use Tulia\Cms\Website\Domain\ReadModel\Model\Locale;
use Tulia\Cms\Website\Domain\ReadModel\Model\Website;

/**
 * @author Adam Banaszkiewicz
 */
final class LocalStorageWebsiteFinder implements WebsiteFinderInterface
{
    public function __construct(
        private readonly DynamicConfigurationInterface $configuration,
    ) {
    }

    protected function fillWebsitesWithLocales(Collection $collection): void
    {
        $locales = $this->connection->fetchAllAssociative(
            'SELECT *, BIN_TO_UUID(website_id) AS website_id FROM #__website_locale whl
            WHERE whl.website_id IN (:websiteIdList)
            ORDER BY `is_default` DESC', [
            'websiteIdList' => array_map(function ($website) {
                return Uuid::fromString($website->getId())->toBinary();
            }, $collection->toArray())
        ], [
            'websiteIdList' => Connection::PARAM_STR_ARRAY,
        ]);

        foreach ($locales as $locale) {
            /** @var Website $website */
            foreach ($collection as $website) {
                if ($website->getId() === $locale['website_id']) {
                    $localeObject = new Locale(
                        $locale['code'],
                        $locale['domain'],
                        $locale['domain_development'],
                        $locale['locale_prefix'],
                        $locale['path_prefix'],
                        $locale['ssl_mode'],
                        (bool) $locale['is_default']
                    );
                    $website->addLocale($localeObject);
                }
            }
        }
    }

    public function all(): array
    {
        return array_map(
            fn($v) => $this->produceWebsiteFromArray($v),
            $this->configuration->get('cms.websites')
        );
    }

    private function produceWebsiteFromArray(array $website): Website
    {
        foreach ($website['locales'] as $key => $locale) {
            $website['locales'][$key] = new Locale(
                $locale['code'],
                $locale['domain'],
                $locale['domain_development'],
                $locale['locale_prefix'],
                $locale['path_prefix'],
                $locale['ssl_mode'],
                (bool) $locale['is_default']
            );
        }

        return Website::buildFromArray($website);
    }
}
