<?php

declare(strict_types=1);

namespace Tulia\Cms\Node\Domain\WriteModel\Model;

use Tulia\Cms\Content\Attributes\Domain\WriteModel\Model\Attribute;
use Tulia\Cms\Node\Domain\WriteModel\Event;
use Tulia\Cms\Node\Domain\WriteModel\Event\AttributeUpdated;
use Tulia\Cms\Node\Domain\WriteModel\Event\NodeDeleted;
use Tulia\Cms\Node\Domain\WriteModel\Event\PurposesUpdated;
use Tulia\Cms\Node\Domain\WriteModel\Exception\CannotDeleteNodeException;
use Tulia\Cms\Node\Domain\WriteModel\Exception\CannotImposePurposeToNodeException;
use Tulia\Cms\Node\Domain\WriteModel\Model\ValueObject\Author;
use Tulia\Cms\Node\Domain\WriteModel\Rules\CanAddPurpose\CanImposePurposeInterface;
use Tulia\Cms\Node\Domain\WriteModel\Rules\CanAddPurpose\CanImposePurposeReasonEnum;
use Tulia\Cms\Node\Domain\WriteModel\Rules\CanDeleteNode\CanDeleteNodeInterface;
use Tulia\Cms\Node\Domain\WriteModel\Rules\CanDeleteNode\CanDeleteNodeReasonEnum;
use Tulia\Cms\Node\Domain\WriteModel\Service\SlugGeneratorStrategy\SlugGeneratorStrategyInterface;
use Tulia\Cms\Shared\Domain\WriteModel\Model\AbstractAggregateRoot;
use Tulia\Cms\Shared\Domain\WriteModel\Model\ValueObject\ImmutableDateTime;

/**
 * @author Adam Banaszkiewicz
 */
final class Node extends AbstractAggregateRoot
{
    protected string $id;
    protected string $type;
    protected string $status = 'draft';
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
        string $locale,
        Author $author
    ) {
        $this->id = $id;
        $this->type = $type;
        $this->locale = $locale;
        $this->author = $author;
        $this->createdAt = $this->updatedAt = new ImmutableDateTime();
        $this->updatedAt = new ImmutableDateTime();
        $this->publishedAt = new ImmutableDateTime();
    }

    public static function createNew(
        string $id,
        string $type,
        string $locale,
        Author $author
    ): self {
        $self = new self($id, $type, $locale, $author);
        $self->recordThat(new Event\NodeCreated($id, $type, $locale, $type));

        return $self;
    }

    public static function fromArray(array $data): self
    {
        $self = new self(
            $data['id'],
            $data['type'],
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
            'id'            => $this->id,
            'type'          => $this->type,
            'published_at'  => $this->publishedAt,
            'published_to'  => $this->publishedTo,
            'created_at'    => $this->createdAt,
            'updated_at'    => $this->updatedAt,
            'status'        => $this->status,
            'author_id'     => $this->author->getId(),
            'category_id'   => $this->getCategoryId(),
            'level'         => $this->level,
            'parent_id'     => $this->parentId,
            'locale'        => $this->locale,
            'title'         => $this->title,
            'slug'          => $this->slug,
            'purposes'      => $this->purposes,
            'attributes'    => $this->attributes,
        ];
    }

    public function delete(CanDeleteNodeInterface $rules): void
    {
        $reason = $rules->decide($this->id);

        if (CanDeleteNodeReasonEnum::OK !== $reason) {
            throw CannotDeleteNodeException::fromReason($reason, $this->id, $this->title);
        }

        $this->recordThat(new NodeDeleted($this->id, $this->type, $this->locale));
    }

    public function getId(): string
    {
        return $this->id;
    }

    public function getType(): string
    {
        return $this->type;
    }

    public function persistAttributes(Attribute ...$attributes): void
    {
        $keysNew = array_keys($attributes);
        $keysOld = array_keys($this->attributes);

        $toAdd = array_diff($keysNew, $keysOld);
        $toRemove = array_diff($keysOld, $keysNew);
        $toUpdate = array_intersect($keysNew, $keysOld);

        foreach ($toRemove as $uri) {
            $this->removeAttribute($uri);
        }
        foreach ($toAdd as $uri) {
            $this->addAttribute($attributes[$uri]);
        }
        foreach ($toUpdate as $uri) {
            $this->addAttribute($attributes[$uri]);
        }

        $this->markAsUpdated();
    }

    public function hasAttribute(string $uri): bool
    {
        return isset($this->attributes[$uri]);
    }

    public function getAttribute(string $uri): Attribute
    {
        return $this->attributes[$uri];
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

    /**
     * @return Attribute[]
     */
    public function getAttributes(): array
    {
        return $this->attributes;
    }

    public function rename(SlugGeneratorStrategyInterface $slugGenerator, string $title, ?string $slug): void
    {
        $this->title = $title;
        $this->slug = $slugGenerator->generate($this->locale, (string) $slug, $title, $this->id);

        $this->markAsUpdated();
    }

    public function setStatus(string $status): void
    {
        $this->status = $status;
    }

    public function getPublishedAt(): ImmutableDateTime
    {
        return $this->publishedAt;
    }

    public function publishNodeAt(ImmutableDateTime $publishedAt): void
    {
        $this->publishedAt = $publishedAt;

        $this->markAsUpdated();
    }

    public function getPublishedTo(): ?ImmutableDateTime
    {
        return $this->publishedTo;
    }

    public function publishNodeTo(ImmutableDateTime $publishedTo): void
    {
        $this->publishedTo = $publishedTo;

        $this->markAsUpdated();
    }

    public function publishNodeForever(): void
    {
        $this->publishedTo = null;

        $this->markAsUpdated();
    }

    public function setAuthor(Author $author): void
    {
        $this->author = $author;

        $this->markAsUpdated();
    }

    public function setParentId(?string $parentId): void
    {
        $this->parentId = $parentId;

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

    public function persistPurposes(CanImposePurposeInterface $rules, string ...$purposes): void
    {
        foreach ($purposes as $purpose) {
            $reason = $rules->decide($this->id, $purpose, $this->purposes);

            if (CanImposePurposeReasonEnum::OK !== $reason) {
                throw CannotImposePurposeToNodeException::fromReason($reason, $purpose, $this->id);
            }
        }

        $this->purposes = $purposes;
        $this->recordThat(new PurposesUpdated(
            $this->id,
            $this->type,
            $this->locale,
            $this->purposes
        ));
        $this->markAsUpdated();
    }

    public function imposePurpose(CanImposePurposeInterface $rules, string $purpose): void
    {
        if (\in_array($purpose, $this->purposes, true)) {
            return;
        }

        $reason = $rules->decide($this->id, $purpose, $this->purposes);

        if (CanImposePurposeReasonEnum::OK !== $reason) {
            throw CannotImposePurposeToNodeException::fromReason($reason, $purpose, $this->id);
        }

        $this->purposes[] = $purpose;
        $this->recordThat(new PurposesUpdated(
            $this->id,
            $this->type,
            $this->locale,
            $this->purposes
        ));
        $this->markAsUpdated();
    }

    private function markAsUpdated(): void
    {
        $this->updatedAt = new ImmutableDateTime();
    }
}
