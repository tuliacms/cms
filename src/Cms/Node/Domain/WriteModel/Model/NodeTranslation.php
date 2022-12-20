<?php

declare(strict_types=1);

namespace Tulia\Cms\Node\Domain\WriteModel\Model;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Tulia\Cms\Content\Attributes\Domain\WriteModel\Model\Attribute as CoreAttribute;
use Tulia\Cms\Content\Attributes\Domain\WriteModel\Model\AttributesAwareAggregateTrait;
use Tulia\Cms\Shared\Domain\WriteModel\Service\SlugGeneratorStrategy\SlugGeneratorStrategyInterface;

/**
 * @author Adam Banaszkiewicz
 * @final
 */
class NodeTranslation
{
    use AttributesAwareAggregateTrait;

    private string $id;
    /** @var Attribute[] */
    private Collection $attributes;
    private ?string $slug = null;

    public function __construct(
        private Node $node,
        private string $locale,
        private string $title,
        public bool $translated = false,
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

    public function isTranslated(): bool
    {
        return $this->translated === true;
    }

    public function isFor(string $locale): bool
    {
        return $locale === $this->locale;
    }

    public function getLocale(): string
    {
        return $this->locale;
    }

    public function getId(): string
    {
        return $this->node->getId();
    }

    public function getNodeType(): string
    {
        return $this->node->getNodeType();
    }

    public function changeTitle(SlugGeneratorStrategyInterface $slugGenerator, string $websiteId, string $title, ?string $slug = null): void
    {
        $this->title = $title;
        $this->slug = $slugGenerator->generateGloballyUnique(
            $this->getId(),
            (string) $slug,
            $title,
            $websiteId,
            $this->locale,
        );
    }

    protected function noticeThatAttributeHasBeenAdded(CoreAttribute $attribute): void {}

    protected function noticeThatAttributeHasBeenRemoved(CoreAttribute $attribute): void {}

    protected function noticeThatAttributeHasBeenUpdated(CoreAttribute $attribute): void {}

    protected function factoryAttributeFromCore(CoreAttribute $attribute): Attribute
    {
        return Attribute::fromCore($this, $attribute, $this->locale);
    }
}
