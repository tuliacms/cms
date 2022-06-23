<?php

declare(strict_types=1);

namespace Tulia\Cms\Content\Attributes\Infrastructure\Persistence\WriteModel;

use Tulia\Cms\Content\Attributes\Domain\WriteModel\AttributesRepositoryInterface;
use Tulia\Cms\Content\Attributes\Domain\WriteModel\Model\Attribute;
use Tulia\Cms\Shared\Domain\WriteModel\UuidGeneratorInterface;
use Tulia\Component\Routing\Website\CurrentWebsiteInterface;

/**
 * @author Adam Banaszkiewicz
 */
class DbalAttributesRepository implements AttributesRepositoryInterface
{
    private DbalWriteStorage $storage;
    private CurrentWebsiteInterface $currentWebsite;
    private UuidGeneratorInterface $uuidGenerator;

    public function __construct(
        DbalWriteStorage $storage,
        CurrentWebsiteInterface $currentWebsite,
        UuidGeneratorInterface $uuidGenerator
    ) {
        $this->storage = $storage;
        $this->currentWebsite = $currentWebsite;
        $this->uuidGenerator = $uuidGenerator;
    }

    public function findAllAggregated(string $type, array $ownerIdList, array $info): array
    {
        $source = $this->storage->find($type, $ownerIdList, array_keys($info), $this->currentWebsite->getLocale()->getCode());
        $result = [];

        foreach ($source as $ownerId => $fields) {
            foreach ($fields as $key => $element) {
                $value = $element['value'];

                if ($info[$element['name']]['has_nonscalar_value']) {
                    try {
                        $value = (array) unserialize(
                            (string) $element['value'],
                            ['allowed_classes' => []]
                        );
                    } catch (\ErrorException $e) {
                        // If error, than empty or cannot be unserialized from singular value
                    }
                }

                $flags = [];

                if ($info[$element['name']]['is_compilable']) {
                    $flags[] = 'compilable';
                }
                if ($info[$element['name']]['is_multilingual']) {
                    $flags[] = 'multilingual';
                }
                if ($info[$element['name']]['has_nonscalar_value']) {
                    $flags[] = 'nonscalar_value';
                }

                $result[$ownerId][$key] = new Attribute(
                    $element['name'],
                    $element['uri'],
                    $value,
                    $element['compiled_value'],
                    $element['payload'],
                    $flags
                );
            }
        }

        return $result;
    }

    public function findAll(string $type, string $ownerId, array $info): array
    {
        return $this->findAllAggregated($type, [$ownerId], $info)[$ownerId] ?? [];
    }

    /**
     * @param Attribute[] $metadata
     */
    public function persist(string $type, string $ownerId, array $attributes): void
    {
        $locale = $this->currentWebsite->getLocale()->getCode();
        $structure = [];

        foreach ($attributes as $uri => $attribute) {
            $structure[$uri] = [
                'id' => $this->uuidGenerator->generate(),
                'value' => $attribute->isNonscalarValue() ? serialize($attribute->getValue()) : $attribute->getValue(),
                'compiled_value' => $attribute->getCompiledValue(),
                'payload' => $attribute->getPayload(),
                'owner_id' => $ownerId,
                'name' => $attribute->getCode(),
                'uri' => $uri,
                'locale' => $locale,
                'type' => $type,
                'is_multilingual' => $attribute->isMultilingual(),
                'is_renderable' => $attribute->is('renderable'),
                'has_nonscalar_value' => $attribute->isNonscalarValue(),
            ];
        }

        $this->storage->persist(
            $structure,
            $this->currentWebsite->getDefaultLocale()->getCode()
        );
    }

    public function delete(string $type, string $ownerId): void
    {
        $this->storage->delete($type, $ownerId);
    }
}
