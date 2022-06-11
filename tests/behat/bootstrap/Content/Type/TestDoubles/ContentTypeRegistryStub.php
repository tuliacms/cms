<?php

declare(strict_types=1);

namespace Tulia\Cms\Tests\Behat\Content\Type\TestDoubles;

use Tulia\Cms\Content\Type\Domain\ReadModel\Model\ContentType;
use Tulia\Cms\Content\Type\Domain\ReadModel\Service\ContentTypeProviderInterface;
use Tulia\Cms\Content\Type\Domain\ReadModel\Service\ContentTypeRegistryInterface;

/**
 * @author Adam Banaszkiewicz
 */
final class ContentTypeRegistryStub implements ContentTypeRegistryInterface
{
    private array $existingContentTypes;

    public function __construct(array $existingContentTypes)
    {
        $this->existingContentTypes = $existingContentTypes;
    }

    public function addProvider(ContentTypeProviderInterface $nodeTypeProvider): void
    {
    }

    public function get(string $type): ContentType
    {

    }

    public function has(string $type): bool
    {
        return in_array($type, $this->existingContentTypes, true);
    }

    public function getTypes(): array
    {
        return [];
    }

    public function all(): \Traversable
    {
        return new ArrayIterator([]);
    }

    public function allByType(string $type): \Traversable
    {
        return new ArrayIterator([]);
    }
}
