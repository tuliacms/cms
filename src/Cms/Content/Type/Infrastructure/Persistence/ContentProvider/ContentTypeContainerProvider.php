<?php

declare(strict_types=1);

namespace Tulia\Cms\Content\Type\Infrastructure\Persistence\ContentProvider;

use Tulia\Cms\Content\Type\Domain\ReadModel\Model\ContentType;
use Tulia\Cms\Content\Type\Domain\ReadModel\Service\ContentTypeProviderInterface;

/**
 * @author Adam Banaszkiewicz
 */
class ContentTypeContainerProvider implements ContentTypeProviderInterface
{
    use SymfonyContainerStandarizableTrait;

    public function __construct(
        private array $configuration
    ) {
    }

    public function provide(): array
    {
        $result = [];

        foreach ($this->configuration as $code => $type) {
            $type['code'] = $code;
            $type['internal'] = true;
            $type = $this->standarizeArray($type);

            $result[] = ContentType::fromArray($type);
        }

        return $result;
    }
}
