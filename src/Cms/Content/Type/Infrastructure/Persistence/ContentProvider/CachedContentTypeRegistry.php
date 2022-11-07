<?php

declare(strict_types=1);

namespace Tulia\Cms\Content\Type\Infrastructure\Persistence\ContentProvider;

use Symfony\Contracts\Cache\CacheInterface;
use Symfony\Contracts\Cache\ItemInterface;
use Tulia\Cms\Content\Type\Domain\ReadModel\Service\ContentTypeDecorator;
use Tulia\Cms\Content\Type\Domain\ReadModel\Service\ContentTypeRegistry;

/**
 * @author Adam Banaszkiewicz
 */
class CachedContentTypeRegistry extends ContentTypeRegistry
{
    public function __construct(
        ContentTypeDecorator $decorator,
        private readonly CacheInterface $contentBuilderCache,
    ) {
        parent::__construct($decorator);
    }

    protected function fetch(): array
    {
        return parent::fetch();
        return $this->contentTypes = $this->contentBuilderCache->get('tulia.content_builder.content_types', function (ItemInterface $item) {
            return parent::fetch();
        });
    }

    public function clearCache(): void
    {
        $this->contentBuilderCache->delete('tulia.content_builder.content_types');
    }
}
