<?php

declare(strict_types=1);

namespace Tulia\Cms\Content\Type\Domain\ReadModel\Service;

use Tulia\Cms\Content\Type\Domain\ReadModel\ContentTypeBuilder\ContentTypeBuilder;
use Tulia\Cms\Content\Type\Domain\ReadModel\ContentTypeBuilder\ContentTypeCollector;
use Tulia\Cms\Content\Type\Domain\ReadModel\Model\ContentType;
use Tulia\Cms\Content\Type\Domain\WriteModel\Exception\ContentTypeNotExistsException;

/**
 * @author Adam Banaszkiewicz
 */
class ContentTypeRegistry implements ContentTypeRegistryInterface
{
    /** @var ContentType[] */
    protected array $contentTypes = [];
    /** @var ContentTypeProviderInterface[] */
    private array $contentTypeProviders = [];

    public function __construct(
        private readonly ContentTypeDecorator $decorator,
        private readonly ContentTypeBuilder $builder,
    ) {
    }

    public function addProvider(ContentTypeProviderInterface $contentTypeProvider): void
    {
        $this->contentTypeProviders[] = $contentTypeProvider;
    }

    /**
     * @throws ContentTypeNotExistsException
     */
    public function get(string $type): ContentType
    {
        $this->fetch();

        if (isset($this->contentTypes[$type]) === false) {
            throw ContentTypeNotExistsException::fromType($type);
        }

        return $this->contentTypes[$type];
    }

    public function has(string $type): bool
    {
        $this->fetch();

        return isset($this->contentTypes[$type]);
    }

    /**
     * @return ContentType[]
     */
    public function getTypes(): array
    {
        $this->fetch();

        return array_keys($this->contentTypes);
    }

    /**
     * @return ContentType[]
     */
    public function all(): \Traversable
    {
        $this->fetch();

        foreach ($this->contentTypes as $contentType) {
            yield $contentType;
        }
    }

    /**
     * @return ContentType[]
     */
    public function allByType(string $type): \Traversable
    {
        $this->fetch();

        foreach ($this->contentTypes as $contentType) {
            if ($contentType->getType() === $type) {
                yield $contentType;
            }
        }
    }

    protected function fetch(): array
    {
        if ($this->contentTypes !== []) {
            return [];
        }

        $collector = new ContentTypeCollector();

        foreach ($this->contentTypeProviders as $provider) {
            $provider->provide($collector);
        }

        $this->decorator->decorate($collector);

        foreach ($collector->all() as $definition) {
            $this->contentTypes[$definition->code] = $this->builder->build($definition);
        }

        return $this->contentTypes;
    }
}
