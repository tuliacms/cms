<?php

declare(strict_types=1);

namespace Tulia\Component\Theme\Resolver;

use Tulia\Component\Theme\Configuration\ConfigurationInterface;
use Tulia\Component\Theme\Customizer\Changeset\DefaultChangesetFactory;
use Tulia\Component\Theme\Customizer\Changeset\Storage\StorageInterface;
use Tulia\Component\Theme\Customizer\DetectorInterface;
use Tulia\Component\Theme\Enum\ChangesetTypeEnum;
use Tulia\Component\Theme\ThemeInterface;

/**
 * @author Adam Banaszkiewicz
 */
class CustomizerResolver implements ResolverInterface
{
    public function __construct(
        private readonly DefaultChangesetFactory $defaultChangesetFactory,
        private readonly StorageInterface $storage,
        private readonly DetectorInterface $detector,
    ) {
    }

    public function resolve(ConfigurationInterface $configuration, ThemeInterface $theme, string $websiteId, string $locale): void
    {
        $changeset = $this->storage->getActiveChangeset($theme->getName(), $websiteId, $locale);

        if (! $changeset) {
            $changeset = $this->defaultChangesetFactory->buildForTheme($theme);
            $changeset->setType(ChangesetTypeEnum::ACTIVE);
        }

        foreach ($changeset as $key => $val) {
            $configuration->add('customizer', $key, $val);
        }

        if ($this->detector->isCustomizerMode()) {
            $this->applyCustomizerAwareChangeset($configuration, $websiteId, $locale);
        }
    }

    private function applyCustomizerAwareChangeset(ConfigurationInterface $configuration, string $websiteId, string $locale): void
    {
        $id = $this->detector->getChangesetId();

        if ($this->storage->has($id, $websiteId, $locale)) {
            $changeset = $this->storage->get($id, $websiteId, $locale);

            foreach ($changeset as $key => $val) {
                $configuration->add('customizer', $key, $val);
            }
        }
    }
}
