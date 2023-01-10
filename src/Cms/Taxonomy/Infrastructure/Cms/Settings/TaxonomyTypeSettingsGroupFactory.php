<?php

declare(strict_types=1);

namespace Tulia\Cms\Taxonomy\Infrastructure\Cms\Settings;

use Tulia\Cms\Content\Type\Domain\ReadModel\Service\ContentTypeRegistryInterface;
use Tulia\Cms\Settings\Domain\Group\AbstractSettingsGroupFactory;

/**
 * @author Adam Banaszkiewicz
 * @final
 * @lazy
 */
class TaxonomyTypeSettingsGroupFactory extends AbstractSettingsGroupFactory
{
    public function __construct(
        private readonly ContentTypeRegistryInterface $contentTypeRegistry,
    ) {
    }

    public function factory(): iterable
    {
        foreach ($this->contentTypeRegistry->allByType('taxonomy') as $type) {
            yield new TaxonomyTypeSettingsGroup($type);
        }
    }
}
