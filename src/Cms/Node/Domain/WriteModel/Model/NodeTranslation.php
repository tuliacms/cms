<?php

declare(strict_types=1);

namespace Tulia\Cms\Node\Domain\WriteModel\Model;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Tulia\Cms\Content\Attributes\Domain\WriteModel\Model\Attribute as CoreAttribute;
use Tulia\Cms\Content\Attributes\Domain\WriteModel\Model\AttributesAwareAggregateTrait;
use Tulia\Cms\Node\Domain\WriteModel\Service\SlugGeneratorStrategy\SlugGeneratorStrategyInterface;

/**
 * @author Adam Banaszkiewicz
 */
final class NodeTranslation
{
    use AttributesAwareAggregateTrait;

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

    protected function noticeThatAttributeHasBeenAdded(CoreAttribute $attribute): void
    {
        $this->markAsUpdated();
    }

    protected function noticeThatAttributeHasBeenRemoved(CoreAttribute $attribute): void
    {
        $this->markAsUpdated();
    }

    protected function noticeThatAttributeHasBeenUpdated(CoreAttribute $attribute): void
    {
        $this->markAsUpdated();
    }

    protected function factoryAttributeFromCore(CoreAttribute $attribute): Attribute
    {
        return Attribute::fromCore($this, $attribute, $this->locale);
    }

    private function markAsUpdated(): void
    {
        ($this->updateCallback)();
    }
}
