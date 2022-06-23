<?php

declare(strict_types=1);

namespace Tulia\Cms\Node\Domain\WriteModel\Model;

use Tulia\Cms\Content\Attributes\Domain\WriteModel\AttributesAwareTrait;
use Tulia\Cms\Content\Attributes\Domain\WriteModel\Model\Attribute;
use Tulia\Cms\Content\Attributes\Domain\WriteModel\Model\AttributesAwareInterface;
use Tulia\Cms\Node\Domain\WriteModel\Event;
use Tulia\Cms\Node\Domain\WriteModel\Event\AttributeUpdated;
use Tulia\Cms\Node\Domain\WriteModel\Event\NodeDeleted;
use Tulia\Cms\Node\Domain\WriteModel\Exception\CannotDeleteNodeException;
use Tulia\Cms\Node\Domain\WriteModel\Model\ValueObject\Author;
use Tulia\Cms\Node\Domain\WriteModel\Model\ValueObject\NodeId;
use Tulia\Cms\Node\Domain\WriteModel\Rules\CanDeleteNode\CanDeleteNodeInterface;
use Tulia\Cms\Node\Domain\WriteModel\Rules\CanDeleteNode\CanDeleteNodeReasonEnum;
use Tulia\Cms\Shared\Domain\WriteModel\Model\AbstractAggregateRoot;
use Tulia\Cms\Shared\Domain\WriteModel\Model\ValueObject\ImmutableDateTime;

/**
 * @author Adam Banaszkiewicz
 */
final class Node extends AbstractAggregateRoot implements AttributesAwareInterface
{
    use AttributesAwareTrait;

    protected NodeId $id;
    protected string $type;
    protected string $status = 'draft';
    protected string $websiteId;
    protected ImmutableDateTime $publishedAt;
    protected ?ImmutableDateTime $publishedTo = null;
    protected ImmutableDateTime $createdAt;
    protected ?ImmutableDateTime $updatedAt = null;
    protected Author $author;
    protected ?string $parentId = null;
    protected int $level = 0;
    protected string $locale;
    protected bool $translated = true;
    protected string $title = '';
    protected ?string $slug = null;
    protected array $purposes = [];
    /** @var Attribute[] */
    protected array $attributes = [];

    private function __construct(
        string $id,
        string $type,
        string $websiteId,
        string $locale,
        Author $author
    ) {
        $this->id = new NodeId($id);
        $this->type = $type;
        $this->websiteId = $websiteId;
        $this->locale = $locale;
        $this->author = $author;
        $this->createdAt = $this->updatedAt = new ImmutableDateTime();
        $this->updatedAt = new ImmutableDateTime();
        $this->publishedAt = new ImmutableDateTime();
    }

    public static function createNew(
        string $id,
        string $type,
        string $websiteId,
        string $locale,
        Author $author
    ): self {
        $self = new self($id, $type, $websiteId, $locale, $author);
        $self->recordThat(new Event\NodeCreated($id, $type, $websiteId, $locale, $type));

        return $self;
    }

    public static function fromArray(array $data): self
    {
        $self = new self(
            $data['id'],
            $data['type'],
            $data['website_id'],
            $data['locale'],
            new Author($data['author_id'])
        );
        $self->status = $data['status'] ?? 'published';
        $self->createdAt = new ImmutableDateTime($data['created_at']);
        $self->updatedAt = $data['updated_at'] ? new ImmutableDateTime($data['updated_at']) : null;
        $self->publishedAt = new ImmutableDateTime($data['published_at']);
        $self->publishedTo = $data['published_to'] ? new ImmutableDateTime($data['published_to']) : null;
        $self->parentId = $data['parent_id'] ?? null;
        $self->title = $data['title'] ?? '';
        $self->slug = $data['slug'] ?? null;
        $self->purposes = $data['purposes'] ?? [];
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
            'author_id'     => $this->author->getId(),
            'category_id'   => $this->getCategoryId(),
            'level'         => $this->getLevel(),
            'parent_id'     => $this->getParentId(),
            'locale'        => $this->getLocale(),
            'title'         => $this->getTitle(),
            'slug'          => $this->getSlug(),
            'purposes'      => $this->getPurposes(),
            'attributes'    => $this->attributes,
        ];
    }

    public function delete(CanDeleteNodeInterface $rules): void
    {
        $reason = $rules->decide($this->id->getValue());

        if (CanDeleteNodeReasonEnum::OK !== $reason) {
            throw CannotDeleteNodeException::fromReason($reason, $this->id->getValue(), $this->title);
        }

        $this->recordThat(new NodeDeleted($this->id->getValue(), $this->type, $this->websiteId, $this->locale));
    }

    public function getId(): NodeId
    {
        return $this->id;
    }

    public function getType(): string
    {
        return $this->type;
    }

    public function addAttribute(Attribute $attribute): void
    {
        $uri = $attribute->getUri();
        $value = $attribute->getValue();

        if (isset($this->attributes[$uri]) === false || $this->attributes[$uri]->equals($attribute) === false) {
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

            $this->markAsUpdated();
        }
    }

    public function removeAttribute(string $uri): void
    {
        unset($this->attributes[$uri]);

        $this->markAsUpdated();
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

    public function getAuthor(): Author
    {
        return $this->author;
    }

    public function setAuthor(Author $author): void
    {
        $this->author = $author;

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

    public function hasPurpose(string $purpose): bool
    {
        return \in_array($purpose, $this->purposes, true);
    }

    public function getPurposes(): array
    {
        return $this->purposes;
    }

    public function updatePurposes(array $purposes): void
    {
        $this->purposes = $purposes;

        /*$oldFlags = array_diff($this->attributes['flags'], $flags);
        $newFlags = array_diff($flags, $this->attributes['flags']);*/
    }

    private function markAsUpdated(): void
    {
        $this->updatedAt = new ImmutableDateTime();
    }
}
