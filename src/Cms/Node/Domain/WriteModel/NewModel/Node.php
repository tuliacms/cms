<?php

declare(strict_types=1);

namespace Tulia\Cms\Node\Domain\WriteModel\NewModel;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Tulia\Cms\Node\Domain\WriteModel\Event;
use Tulia\Cms\Node\Domain\WriteModel\Exception\CannotImposePurposeToNodeException;
use Tulia\Cms\Node\Domain\WriteModel\Exception\NodeTranslationDoesntExists;
use Tulia\Cms\Node\Domain\WriteModel\Rules\CanAddPurpose\CanImposePurposeInterface;
use Tulia\Cms\Node\Domain\WriteModel\Rules\CanAddPurpose\CanImposePurposeReasonEnum;
use Tulia\Cms\Shared\Domain\WriteModel\Model\AbstractAggregateRoot;
use Tulia\Cms\Shared\Domain\WriteModel\Model\ValueObject\ImmutableDateTime;

/**
 * @author Adam Banaszkiewicz
 * @final
 */
class Node extends AbstractAggregateRoot
{
    /** @var NodeTranslation[] */
    private Collection $translations;
    private ImmutableDateTime $publishedAt;
    private ?ImmutableDateTime $publishedTo = null;
    private ImmutableDateTime $createdAt;
    private ?ImmutableDateTime $updatedAt = null;
    private string $status = 'draft';
    private ?Node $parentNode = null;
    private int $level = 0;
    /** @var Purpose[] */
    private Collection $purposes;

    private function __construct(
        private string $id,
        private string $type,
        private string $author
    ) {
    }

    public static function create(
        string $id,
        string $type,
        string $author
    ): self {
        $self = new self($id, $type, $author);
        $self->createdAt = new ImmutableDateTime();
        $self->translations = new ArrayCollection();
        $self->purposes = new ArrayCollection();
        $self->recordThat(new Event\NodeCreated($id, $type));

        return $self;
    }

    public static function fromArray(array $data): self
    {
        $self = new self(
            $data['id'],
            $data['type'],
            $data['author_id']
        );
        $self->status = $data['status'] ?? 'published';
        $self->createdAt = new ImmutableDateTime($data['created_at']);
        $self->updatedAt = $data['updated_at'] ? new ImmutableDateTime($data['updated_at']) : null;
        $self->publishedAt = new ImmutableDateTime($data['published_at']);
        $self->publishedTo = $data['published_to'] ? new ImmutableDateTime($data['published_to']) : null;
        $self->parentId = $data['parent_id'] ?? null;
        $self->purposes = $data['purposes'] ?? [];
        $self->level = (int) ($data['level'] ?? 0);

        return $self;
    }

    public function toArray(string $locale, string $defaultLocale): array
    {
        $translation = null;
        $isTranslated = false;
        $attributes = [];
        $title = '';
        $slug = '';

        if ($this->isTranslatedTo($locale)) {
            $translation = $this->trans($locale)->toArray();
            $isTranslated = true;
        } elseif ($this->isTranslatedTo($defaultLocale)) {
            $translation = $this->trans($defaultLocale)->toArray();
        }

        if ($translation) {
            $attributes = $translation['attributes'];
            $title = $translation['title'];
            $slug = $translation['slug'];
        }

        $purposes = [];

        foreach ($this->purposes as $purpose) {
            $purposes[] = (string) $purpose;
        }

        return [
            'id'            => $this->id,
            'type'          => $this->type,
            'published_at'  => $this->publishedAt->toStringWithPrecision(),
            'published_to'  => $this->publishedTo?->toStringWithPrecision(),
            'created_at'    => $this->createdAt->toStringWithPrecision(),
            'updated_at'    => $this->updatedAt?->toStringWithPrecision(),
            'status'        => $this->status,
            'author_id'     => $this->author,
            'level'         => $this->level,
            'parent_id'     => $this->parentNode?->id,
            'purposes'      => $purposes,
            'title'         => $title,
            'slug'          => $slug,
            'attributes'    => $attributes,
            'is_translated' => $isTranslated,
        ];
    }

    public function getId(): string
    {
        return $this->id;
    }

    public function getNodeType(): string
    {
        return $this->type;
    }

    private function trans(string $locale): NodeTranslation
    {
        foreach ($this->translations as $translation) {
            if ($translation->isFor($locale)) {
                $translation->setUpdateCallback(function () {
                    $this->markAsUpdated();
                });
                return $translation;
            }
        }

        throw NodeTranslationDoesntExists::fromLocale($this->id, $locale);
    }

    public function isTranslatedTo(string $locale): bool
    {
        foreach ($this->translations as $translation) {
            if ($translation->isFor($locale)) {
                return true;
            }
        }

        return false;
    }

    public function translate(string $locale): NodeTranslation
    {
        if ($this->isTranslatedTo($locale)) {
            $translation = $this->trans($locale);
        } else {
            $translation = new NodeTranslation($this, $locale);
            $this->translations->add($translation);
        }

        $translation->setUpdateCallback(function () {
            $this->markAsUpdated();
        });

        return $translation;
    }

    public function publish(ImmutableDateTime $publishedAt): void
    {
        $this->publishedAt = $publishedAt;

        $this->recordThat(new Event\NodePublished($this->id, $this->type, $this->publishedAt));
    }

    public function setStatus(string $status): void
    {
        $this->status = $status;
        $this->markAsUpdated();
    }

    public function moveAsChildOf(Node $parent, NodeLevelCalculatorInterface $calculator): void
    {
        $this->parent = $parent;
        $this->level = $calculator->calculate($this->id);
        $this->markAsUpdated();
    }

    public function moveAsRootNode(): void
    {
        $this->parent = null;
        $this->level = 0;
        $this->markAsUpdated();
    }

    public function persistPurposes(CanImposePurposeInterface $rules, string ...$purposes): void
    {
        if (empty($purposes)) {
            foreach ($this->purposes as $purpose) {
                $this->purposes->removeElement($purpose);
            }

            return;
        }

        $keysNew = array_values($purposes);
        $keysOld = array_map(
            static fn(Purpose $v) => (string) $v,
            $this->purposes->toArray()
        );

        $toAdd = array_diff($keysNew, $keysOld);
        $toRemove = array_diff($keysOld, $keysNew);

        foreach ($toRemove as $purposeCode) {
            foreach ($this->purposes as $purpose) {
                if ($purpose->is($purposeCode)) {
                    $this->purposes->removeElement($purpose);
                }
            }
        }

        foreach ($toAdd as $purposeCode) {
            $purpose = new Purpose($this, $purposeCode);

            $reason = $rules->decide($this->id, $purpose, ...$this->purposes);

            if (CanImposePurposeReasonEnum::OK !== $reason) {
                throw CannotImposePurposeToNodeException::fromReason($reason, (string) $purpose, $this->id);
            }

            $this->purposes->add($purpose);
        }

        $this->recordThat(new Event\PurposesUpdated(
            $this->id,
            $this->type,
            array_map(
                static fn(Purpose $v) => (string) $v,
                $this->purposes->toArray()
            )
        ));
        $this->markAsUpdated();
    }

    public function imposePurpose(CanImposePurposeInterface $rules, Purpose $purpose): void
    {
        if (\in_array($purpose, $this->purposes, true)) {
            return;
        }

        $reason = $rules->decide($this->id, $purpose, ...$this->purposes);

        if (CanImposePurposeReasonEnum::OK !== $reason) {
            throw CannotImposePurposeToNodeException::fromReason($reason, (string) $purpose, $this->id);
        }

        $this->purposes->add($purpose);
        $this->recordThat(new Event\PurposesUpdated(
            $this->id,
            $this->type,
            $this->purposes
        ));
        $this->markAsUpdated();
    }

    public function publishNodeAt(ImmutableDateTime $publishedAt): void
    {
        $this->publishedAt = $publishedAt;

        $this->markAsUpdated();
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

    public function delete(): void
    {
        $this->recordThat(new Event\NodeDeleted($this->id, $this->type));
    }

    private function markAsUpdated(): void
    {
        $this->updatedAt = new ImmutableDateTime();
    }
}
