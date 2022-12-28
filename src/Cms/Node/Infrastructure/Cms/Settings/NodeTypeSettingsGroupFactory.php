<?php

declare(strict_types=1);

namespace Tulia\Cms\Node\Infrastructure\Cms\Settings;

use Tulia\Cms\Content\Type\Domain\ReadModel\Service\ContentTypeRegistryInterface;
use Tulia\Cms\Settings\Domain\Group\AbstractSettingsGroupFactory;

/**
 * @author Adam Banaszkiewicz
 * @final
 * @lazy
 */
class NodeTypeSettingsGroupFactory extends AbstractSettingsGroupFactory
{
    public function __construct(
        private readonly ContentTypeRegistryInterface $contentTypeRegistry,
    ) {
    }

    public function factory(): iterable
    {
        foreach ($this->contentTypeRegistry->allByType('node') as $type) {
            yield new NodeTypeSettingsGroup($type);
        }
    }
}
