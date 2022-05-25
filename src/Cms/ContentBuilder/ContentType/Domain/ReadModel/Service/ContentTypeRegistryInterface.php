<?php

declare(strict_types=1);

namespace Tulia\Cms\ContentBuilder\ContentType\Domain\ReadModel\Service;

use Tulia\Cms\ContentBuilder\ContentType\Domain\ReadModel\Model\ContentType;

/**
 * @author Adam Banaszkiewicz
 */
interface ContentTypeRegistryInterface
{
    public function addProvider(ContentTypeProviderInterface $nodeTypeProvider): void;
    public function get(string $type): ContentType;
    public function has(string $type): bool;
    public function getTypes(): array;

    /**
     * @return ContentType[]
     */
    public function all(): \Traversable;
    public function allByType(string $type): \Traversable;
}