<?php

declare(strict_types=1);

namespace Tulia\Cms\Node\Domain\WriteModel\NewModel;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Tulia\Cms\Content\Attributes\Domain\WriteModel\Model\Attribute;
use Tulia\Cms\Node\Domain\WriteModel\NewModel\Attribute as NewCoreAttribute;
use Tulia\Cms\Node\Domain\WriteModel\Service\SlugGeneratorStrategy\SlugGeneratorStrategyInterface;

/**
 * @author Adam Banaszkiewicz
 */
final class NodeTranslation
{
    private \Closure $updateCallback;
    private string $id;
    /** @var Attribute[] */
    private Collection $attributes;
    private ?string $slug = null;
    private string $title = '';

    public function __construct(
        private Node $node,
        private string $locale
    ) {
        $this->attributes = new ArrayCollection();
    }

    public function toArray(): array
    {
        return [
            'title' => $this->title,
            'slug' => $this->slug,
            'attributes' => $this->attributesToArray(),
        ];
    }

    public function isFor(string $locale): bool
    {
        return $locale === $this->locale;
    }

    public function setUpdateCallback(\Closure $updateCallback): void
    {
        $this->updateCallback = $updateCallback;
    }

    public function getId(): string
    {
        return $this->node->getId();
    }

    public function getNodeType(): string
    {
        return $this->node->getNodeType();
    }

    public function rename(SlugGeneratorStrategyInterface $slugGenerator, string $title, ?string $slug): void
    {
        $this->title = $title;
        $this->slug = $slugGenerator->generate($this->getId(), (string) $slug, $title, $this->locale);

        $this->markAsUpdated();
    }

    public function persistAttributes(Attribute ...$sourceAttributes): void
    {
        $attributes = $this->repackAttributes($sourceAttributes);

        $keysNew = array_keys($attributes);
        $keysOld = array_keys($this->attributesToArray());

        $toAdd = array_diff($keysNew, $keysOld);
        $toRemove = array_diff($keysOld, $keysNew);
        $toUpdate = array_intersect($keysNew, $keysOld);

        foreach ($toRemove as $uri) {
            $this->removeAttribute($uri);
        }

        foreach ($toAdd as $uri) {
            if ($attributes[$uri]->isEmpty()) {
                $this->removeAttribute($uri);
            } else {
                $this->addAttribute(NewCoreAttribute::fromCore($this, $attributes[$uri], $this->locale));
            }
        }

        foreach ($toUpdate as $uri) {
            if ($attributes[$uri]->isEmpty()) {
                $this->removeAttribute($uri);
            } else {
                $this->addAttribute(NewCoreAttribute::fromCore($this, $attributes[$uri], $this->locale));
            }
        }
    }

    public function hasAttribute(string $uri): bool
    {
        return isset($this->attributes[$uri]);
    }

    public function getAttribute(string $uri): NewCoreAttribute
    {
        return $this->attributes[$uri];
    }

    public function addAttribute(NewCoreAttribute $attribute): void
    {
        /** @var null|Attribute $found */
        $found = null;

        foreach ($this->attributes as $pretendend) {
            if ($pretendend->getUri() === $attribute->getUri()) {
                $found = $pretendend;
            }
        }

        if (!$found) {
            $this->attributes->add($attribute);
            $this->markAsUpdated();
            return;
        }

        if ($found->equals($attribute) === false) {
            $found->detach();
            $this->attributes->removeElement($found);
            $this->attributes->add($attribute);
            $this->markAsUpdated();
        }
    }

    public function removeAttribute(string $uri): void
    {
        foreach ($this->attributes as $attribute) {
            if ($attribute->getUri() === $uri) {
                $attribute->detach();
                $this->attributes->removeElement($attribute);

                $this->markAsUpdated();
            }
        }
    }

    private function repackAttributes(array $attributes): array
    {
        $repackedAttributes = [];

        foreach ($attributes as $attribute) {
            $repackedAttributes[$attribute->getUri()] = $attribute;
        }

        return $repackedAttributes;
    }

    private function attributesToArray(): array
    {
        $attributes = [];

        /** @var Attribute $attribute */
        foreach ($this->attributes->toArray() as $attribute) {
            $attributes[$attribute->getUri()] = $attribute;
        }

        return $attributes;
    }

    private function markAsUpdated(): void
    {
        ($this->updateCallback)();
    }
}
