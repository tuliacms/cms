<?php

declare(strict_types=1);

namespace Tulia\Cms\Node\Domain\WriteModel\Model;

use Tulia\Cms\Attributes\Domain\WriteModel\Model\Attribute;
use Tulia\Cms\Node\Domain\WriteModel\Event;
use Tulia\Cms\Node\Domain\WriteModel\Event\AttributeUpdated;
use Tulia\Cms\Node\Domain\WriteModel\Model\ValueObject\NodeId;
use Tulia\Cms\Shared\Domain\WriteModel\Model\AggregateRoot;
use Tulia\Cms\Shared\Domain\WriteModel\Model\ValueObject\ImmutableDateTime;

/**
 * @author Adam Banaszkiewicz
 */
final class Node extends AggregateRoot
{
    protected NodeId $id;
    protected string $type;
    protected string $status = 'draft';
    protected string $websiteId;
    protected ImmutableDateTime $publishedAt;
    protected ?ImmutableDateTime $publishedTo = null;
    protected ImmutableDateTime $createdAt;
    protected ?ImmutableDateTime $updatedAt = null;
    protected ?string $authorId = null;
    protected ?string $parentId = null;
    protected int $level = 0;
    protected string $locale;
    protected bool $translated = true;
    protected string $title = '';
    protected ?string $slug = null;
    /** @var Attribute[] */
    protected array $attributes = [];

    private function __construct(
        string $id,
        string $type,
        string $websiteId,
        string $locale
    ) {
        $this->id = new NodeId($id);
        $this->type = $type;
        $this->websiteId = $websiteId;
        $this->locale = $locale;
        $this->createdAt = $this->updatedAt = new ImmutableDateTime();
        $this->updatedAt = new ImmutableDateTime();
        $this->publishedAt = new ImmutableDateTime();
    }

    public static function createNew(
        string $id,
        string $type,
        string $websiteId,
        string $locale
    ): self {
        $self = new self($id, $type, $websiteId, $locale);
        $self->recordThat(new Event\NodeCreated($id, $type, $websiteId, $locale, $type));

        return $self;
    }

    public static function fromArray(array $data): self
    {
        $self = new self(
            $data['id'],
            $data['type'],
            $data['website_id'],
            $data['locale']
        );
        $self->status = $data['status'] ?? 'published';
        $self->createdAt = new ImmutableDateTime($data['created_at']);
        $self->updatedAt = $data['updated_at'] ? new ImmutableDateTime($data['updated_at']) : null;
        $self->publishedAt = new ImmutableDateTime($data['published_at']);
        $self->publishedTo = $data['published_to'] ? new ImmutableDateTime($data['published_to']) : null;
        $self->authorId = $data['author_id'] ?? null;
        $self->parentId = $data['parent_id'] ?? null;
        $self->title = $data['title'] ?? '';
        $self->slug = $data['slug'] ?? null;
        $self->level = (int) ($data['level'] ?? 0);
        $self->translated = (bool) ($data['translated'] ?? true);

        $self->attributes = $data['attributes'];

        return $self;
    }

    public function toArray(): array
    {
        return [
            'id'            => $this->getId()->getValue(),
            'type'          => $this->getType(),
            'website_id'    => $this->getWebsiteId(),
            'published_at'  => $this->getPublishedAt(),
            'published_to'  => $this->getPublishedTo(),
            'created_at'    => $this->getCreatedAt(),
            'updated_at'    => $this->getUpdatedAt(),
            'status'        => $this->getStatus(),
            'author_id'     => $this->getAuthorId(),
            'category_id'   => $this->getCategoryId(),
            'level'         => $this->getLevel(),
            'parent_id'     => $this->getParentId(),
            'locale'        => $this->getLocale(),
            'title'         => $this->getTitle(),
            'slug'          => $this->getSlug(),
            'attributes'    => $this->attributes,
        ];
    }

    public function getId(): NodeId
    {
        return $this->id;
    }

    public function getType(): string
    {
        return $this->type;
    }

    /**
     * @param Attribute[] $attributes
     */
    public function updateAttributes(array $attributes): void
    {
        unset(
            $attributes['id'],
            $attributes['type'],
            $attributes['status'],
            $attributes['title'],
            $attributes['slug'],
            $attributes['parent_id'],
            $attributes['published_at'],
            $attributes['published_to'],
            $attributes['author_id'],
        );

        foreach ($attributes as $attribute) {
            $this->addAttribute($attribute);
        }

        $this->markAsUpdated();
    }

    /**
     * @return Attribute[]
     */
    public function getAttributes(): array
    {
        return $this->attributes;
    }

    public function addAttribute(Attribute $attribute): void
    {
        $uri = $attribute->getUri();
        $value = $attribute->getValue();

        if (isset($this->attributes[$uri]) === false || $this->attributes[$uri]->getValue() !== $value) {
            $this->attributes[$attribute->getUri()] = $attribute;
            /**
             * Calling recordUniqueThat() prevents the system to record multiple changes on the same attribute.
             * This may be caused, in example, by SlugGenerator: first time system sets raw value from From,
             * and then SlugGenerator sets the validated and normalized slug. For us, the last updated
             * attribute's value matters, so we remove all previous events and adds new, at the end of
             * collection.
             */
            $this->recordUniqueThat(AttributeUpdated::fromNode($this, $uri, $value), function ($event) use ($uri) {
                if ($event instanceof AttributeUpdated) {
                    return $uri === $event->getAttribute();
                }
            });
        }
    }

    public function hasAttribute(string $uri): bool
    {
        return isset($this->attributes[$uri]);
    }

    public function removeAttribute(string $uri): void
    {
        unset($this->attributes[$uri]);

        $this->markAsUpdated();
    }

    /**
     * @param mixed $default
     * @return mixed
     */
    public function getAttribute(string $uri, $default = null)
    {
        return $this->attributes[$uri] ?? $default;
    }

    public function hasFlag(string $flag): bool
    {
        return isset($this->attributes['flags']) && \in_array($flag, $this->attributes['flags']->getValue(), true);
    }

    public function getFlags(): array
    {
        return isset($this->attributes['flags']) ? $this->attributes['flags']->getValue() : [];
    }

    public function getTitle(): string
    {
        return $this->title;
    }

    public function setTitle(string $title): void
    {
        $this->title = $title;
    }

    public function getSlug(): ?string
    {
        return $this->slug;
    }

    public function setSlug(?string $slug): void
    {
        $this->slug = $slug;
    }

    public function getStatus(): string
    {
        return $this->status;
    }

    public function setStatus(string $status): void
    {
        $this->status = $status;
    }

    public function getWebsiteId(): string
    {
        return $this->websiteId;
    }

    public function getPublishedAt(): ImmutableDateTime
    {
        return $this->publishedAt;
    }

    public function setPublishedAt(ImmutableDateTime $publishedAt): void
    {
        $this->publishedAt = $publishedAt;

        $this->markAsUpdated();
    }

    public function getPublishedTo(): ?ImmutableDateTime
    {
        return $this->publishedTo;
    }

    public function setPublishedTo(ImmutableDateTime $publishedTo): void
    {
        $this->publishedTo = $publishedTo;

        $this->markAsUpdated();
    }

    public function setPublishedToForever(): void
    {
        $this->publishedTo = null;

        $this->markAsUpdated();
    }

    public function getCreatedAt(): ImmutableDateTime
    {
        return $this->createdAt;
    }

    public function getUpdatedAt(): ?ImmutableDateTime
    {
        return $this->updatedAt;
    }

    public function getAuthorId(): ?string
    {
        return $this->authorId;
    }

    public function setAuthorId(?string $authorId): void
    {
        $this->authorId = $authorId;

        $this->markAsUpdated();
    }

    public function getParentId(): ?string
    {
        return $this->parentId;
    }

    public function setParentId(?string $parentId): void
    {
        $this->parentId = $parentId;

        $this->markAsUpdated();
    }

    public function getLevel(): int
    {
        return $this->level;
    }

    public function setLevel(int $level): void
    {
        $this->level = $level;

        $this->markAsUpdated();
    }

    public function getCategoryId(): ?string
    {
        return isset($this->attributes['category']) ? $this->attributes['category']->getValue() : null;
    }

    public function getLocale(): string
    {
        return $this->locale;
    }

    public function isTranslated(): bool
    {
        return $this->translated;
    }

    public function setTranslated(bool $translated): void
    {
        $this->translated = $translated;
    }

    private function updateFlags(array $flags): void
    {
        $this->attributes['flags'] = $this->attributes['flags'] ?? [];

        /*$oldFlags = array_diff($this->attributes['flags'], $flags);
        $newFlags = array_diff($flags, $this->attributes['flags']);*/
    }

    private function markAsUpdated(): void
    {
        $this->updatedAt = new ImmutableDateTime();
    }
}
