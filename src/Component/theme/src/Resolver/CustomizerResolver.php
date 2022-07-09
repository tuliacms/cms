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
        private DefaultChangesetFactory $defaultChangesetFactory,
        private StorageInterface $storage,
        private DetectorInterface $detector,
    ) {
    }

    public function resolve(ConfigurationInterface $configuration, ThemeInterface $theme): void
    {
        $changeset = $this->storage->getActiveChangeset($theme->getName(), 'en_US');

        if (! $changeset) {
            $changeset = $this->defaultChangesetFactory->buildForTheme($theme);
            $changeset->setType(ChangesetTypeEnum::ACTIVE);
        }

        foreach ($changeset as $key => $val) {
            $configuration->add('customizer', $key, $val);
        }

        if ($this->detector->isCustomizerMode()) {
            $this->applyCustomizerAwareChangeset($configuration);
        }
    }

    private function applyCustomizerAwareChangeset(ConfigurationInterface $configuration): void
    {
        $id = $this->detector->getChangesetId();

        if ($this->storage->has($id, 'en_US')) {
            $changeset = $this->storage->get($id, 'en_US');

            foreach ($changeset as $key => $val) {
                $configuration->add('customizer', $key, $val);
            }
        }
    }
}
