<?php

declare(strict_types=1);

namespace Tulia\Cms\Node\Domain\ReadModel\Model;

use InvalidArgumentException;
use Tulia\Cms\Content\Attributes\Domain\ReadModel\LazyMagickAttributesTrait;
use Tulia\Cms\Content\Attributes\Domain\ReadModel\Model\AttributesAwareInterface;
use Tulia\Cms\Shared\Domain\WriteModel\Model\ValueObject\ImmutableDateTime;

/**
 * @author Adam Banaszkiewicz
 */
class Node implements AttributesAwareInterface
{
    use LazyMagickAttributesTrait;

    protected string $id;
    protected string $type;
    protected string $status;
    protected ImmutableDateTime $publishedAt;
    protected ?ImmutableDateTime $publishedTo;
    protected ImmutableDateTime $createdAt;
    protected ?ImmutableDateTime $updatedAt;
    protected string $authorId;
    protected ?string $parentId;
    protected int $level;
    protected string $locale;
    protected string $websiteId;
    protected ?string $title;
    protected ?string $slug;
    protected bool $visibility;
    protected array $purposes = [];
    protected ?string $mainCategory = null;
    protected array $additionalCategories = [];

    public static function buildFromArray(array $data): self
    {
        $node = new self();

        if (isset($data['id']) === false) {
            throw new InvalidArgumentException('Node ID must be provided.');
        }

        if (isset($data['locale']) === false) {
            $data['locale'] = 'en_US';
        }

        $data = static::setDatetime($data, 'published_at', new ImmutableDateTime());
        $data = static::setDatetime($data, 'published_to');
        $data = static::setDatetime($data, 'created_at', new ImmutableDateTime());
        $data = static::setDatetime($data, 'updated_at');

        $node->id = $data['id'];
        $node->type = $data['type'] ?? 'page';
        $node->setStatus($data['status'] ?? 'published');
        $node->setPublishedAt($data['published_at']);
        $node->setPublishedTo($data['published_to']);
        $node->setCreatedAt($data['created_at']);
        $node->setUpdatedAt($data['updated_at']);
        $node->setAuthorId($data['author']);
        $node->setParentId($data['parent_id'] ?? null);
        $node->setLevel((int) ($data['level'] ?? 0));
        $node->setLocale($data['locale']);
        $node->websiteId = $data['website_id'];
        $node->setTitle($data['title'] ?? '');
        $node->setSlug($data['slug'] ?? '');
        $node->setPurposes($data['purposes'] ?? []);
        $node->mainCategory = $data['main_category'] ?? null;
        $node->additionalCategories = $data['additional_categories'] ?? [];

        if (isset($data['lazy_attributes'])) {
            $node->attributesLazyStorage = $data['lazy_attributes'];
        }

        return $node;
    }

    private static function setDatetime(array $data, string $key, $default = null): array
    {
        if (\array_key_exists($key, $data) === false) {
            $data[$key] = $default;
        } elseif ($data[$key] === null && $default === null) {
            // Do nothing, allow to null;
        } elseif ($data[$key] instanceof ImmutableDateTime === false) {
            $data[$key] = new ImmutableDateTime($data[$key]);
        }

        return $data;
    }

    public function getId(): string
    {
        return $this->id;
    }

    public function getType(): string
    {
        return $this->type;
    }

    public function getStatus(): string
    {
        return $this->status;
    }

    public function setStatus(string $status): void
    {
        $this->status = $status;
    }

    public function getPublishedAt(): ImmutableDateTime
    {
        return $this->publishedAt;
    }

    public function setPublishedAt(ImmutableDateTime $publishedAt): void
    {
        $this->publishedAt = $publishedAt;
    }

    public function getPublishedTo(): ?ImmutableDateTime
    {
        return $this->publishedTo;
    }

    public function setPublishedTo(?ImmutableDateTime $publishedTo): void
    {
        $this->publishedTo = $publishedTo;
    }

    public function getCreatedAt(): ImmutableDateTime
    {
        return $this->createdAt;
    }

    public function setCreatedAt(ImmutableDateTime $createdAt): void
    {
        $this->createdAt = $createdAt;
    }

    public function getUpdatedAt(): ?ImmutableDateTime
    {
        return $this->updatedAt;
    }

    public function setUpdatedAt(?ImmutableDateTime $updatedAt): void
    {
        $this->updatedAt = $updatedAt;
    }

    public function getAuthorId(): string
    {
        return $this->authorId;
    }

    public function setAuthorId(string $authorId): void
    {
        $this->authorId = $authorId;
    }

    public function getParentId(): ?string
    {
        return $this->parentId;
    }

    public function setParentId(?string $parentId): void
    {
        $this->parentId = $parentId;
    }

    public function getLevel(): int
    {
        return $this->level;
    }

    public function setLevel(int $level): void
    {
        $this->level = $level;
    }

    public function getWebsiteId(): ?string
    {
        return $this->websiteId;
    }

    public function getLocale(): ?string
    {
        return $this->locale;
    }

    public function setLocale(?string $locale): void
    {
        $this->locale = $locale;
    }

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function setTitle(?string $title): void
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

    public function hasPurpose(string $purpose): bool
    {
        return in_array($purpose, $this->purposes);
    }

    public function getPurposes(): array
    {
        return $this->purposes;
    }

    public function setPurposes(array $purposes): void
    {
        $this->purposes = $purposes;
    }

    public function getMainCategory(): ?string
    {
        return $this->mainCategory;
    }

    public function getAdditionalCategories(): array
    {
        return $this->additionalCategories;
    }
}
