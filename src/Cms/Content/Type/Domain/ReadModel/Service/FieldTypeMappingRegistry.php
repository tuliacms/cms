<?php

declare(strict_types=1);

namespace Tulia\Cms\Content\Type\Domain\ReadModel\Service;

use Tulia\Cms\Content\Type\Domain\ReadModel\Exception\FieldTypeNotExistsException;
use Tulia\Cms\Content\Type\Domain\ReadModel\FieldChoicesProvider\FieldChoicesProviderRegistry;
use Tulia\Cms\Content\Type\Domain\ReadModel\FieldTypeBuilder\FieldTypeBuilderInterface;
use Tulia\Cms\Content\Type\Domain\ReadModel\FieldTypeBuilder\FieldTypeBuilderRegistry;
use Tulia\Cms\Content\Type\Domain\ReadModel\FieldTypeHandler\FieldTypeHandlerInterface;
use Tulia\Cms\Content\Type\Domain\ReadModel\FieldTypeHandler\FieldTypeHandlerRegistry;

/**
 * @author Adam Banaszkiewicz
 */
class FieldTypeMappingRegistry
{
    private array $mapping = [];
    private bool $mappingResolved = false;

    public function __construct(
        private readonly ConstraintsResolverInterface $constraintsResolver,
        private readonly FieldTypeBuilderRegistry $builderRegistry,
        private readonly FieldTypeHandlerRegistry $handlerRegistry,
        private readonly FieldChoicesProviderRegistry $choicesProviderRegistry,
    ) {
    }

    public function addMapping(string $type, array $mapingInfo): void
    {
        $this->mapping[$type] = $mapingInfo;
    }

    public function all(): array
    {
        $this->resolveMapping();

        return $this->mapping;
    }

    public function allForContentType(string $contentTypeCode): array
    {
        $this->resolveMapping();

        $result = [];

        foreach ($this->mapping as $type => $map) {
            if (\in_array($contentTypeCode, $map['exclude_for_types'], true)) {
                continue;
            }

            if (
                $map['only_for_types'] !== []
                && \in_array($contentTypeCode, $map['only_for_types'], true) === false
            ) {
                continue;
            }

            $result[$type] = $map;
        }

        return $result;
    }

    /**
     * @throws FieldTypeNotExistsException
     */
    public function getTypeClassname(string $type): string
    {
        $this->resolveMapping();

        if (isset($this->mapping[$type]['classname']) === false) {
            throw FieldTypeNotExistsException::fromName($type);
        }

        return $this->mapping[$type]['classname'];
    }

    public function getTypeHandler(string $type): ?FieldTypeHandlerInterface
    {
        $this->resolveMapping();

        if (isset($this->mapping[$type]['handler']) === false) {
            return null;
        }

        return $this->handlerRegistry->has($this->mapping[$type]['handler'])
            ? $this->handlerRegistry->get($this->mapping[$type]['handler'])
            : null;
    }

    public function getTypeBuilder(string $type): ?FieldTypeBuilderInterface
    {
        $this->resolveMapping();

        if (isset($this->mapping[$type]['builder']) === false) {
            return null;
        }

        return $this->builderRegistry->has($this->mapping[$type]['builder'])
            ? $this->builderRegistry->get($this->mapping[$type]['builder'])
            : null;
    }

    /**
     * @return array{ is_multiple: bool }
     */
    public function get(string $type): array
    {
        $this->resolveMapping();

        return $this->mapping[$type];
    }

    public function getTypeFlags(string $type): array
    {
        $this->resolveMapping();

        return isset($this->mapping[$type]) ? $this->mapping[$type]['flags'] : [];
    }

    public function hasType(string $type): bool
    {
        $this->resolveMapping();

        return isset($this->mapping[$type]);
    }

    private function resolveMapping(): void
    {
        if ($this->mappingResolved) {
            return;
        }

        /**
         * @todo Must be a better way to deal with it.
         *
         * Setting this here, before loop is a hacky hack for choices_provider.
         * When TaxonomyTypeaheadTypeChoicesProvider provides choices, it uses the ContentTypeRegistryInterface
         * to get types by type. And ContentTypeRegistryInterface rounds around to here to
         * retrieve field mapping and wants to resolve it again.
         *
         * For now it must be like this, but maybe there is a better way to prevent this problem.
         */
        $this->mappingResolved = true;

        foreach ($this->mapping as $typeKey => $val) {
            $this->mapping[$typeKey] = $this->constraintsResolver->resolve($this->mapping[$typeKey]);

            foreach ($this->mapping[$typeKey]['configuration'] as $key => $config) {
                if ($config['choices_provider']) {
                    $this->mapping[$typeKey]['configuration'][$key]['choices']
                        = $this->choicesProviderRegistry->has($config['choices_provider'])
                        ? $this->choicesProviderRegistry->get($config['choices_provider'])->provide()
                        : $config['choices'];
                }
            }
        }
    }
}
