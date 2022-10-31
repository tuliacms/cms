<?php

declare(strict_types=1);

namespace Tulia\Cms\Node\Domain\WriteModel\Model;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Tulia\Cms\Node\Domain\WriteModel\Event;
use Tulia\Cms\Node\Domain\WriteModel\Event\NodeUpdated;
use Tulia\Cms\Node\Domain\WriteModel\Exception\CannotDeleteNodeException;
use Tulia\Cms\Node\Domain\WriteModel\Exception\CannotImposePurposeToNodeException;
use Tulia\Cms\Node\Domain\WriteModel\Exception\NodeTranslationDoesntExists;
use Tulia\Cms\Node\Domain\WriteModel\Rules\CanAddPurpose\CanImposePurposeInterface;
use Tulia\Cms\Node\Domain\WriteModel\Rules\CanAddPurpose\CanImposePurposeReasonEnum;
use Tulia\Cms\Node\Domain\WriteModel\Rules\CanDeleteNode\CanDeleteNodeInterface;
use Tulia\Cms\Node\Domain\WriteModel\Rules\CanDeleteNode\CanDeleteNodeReasonEnum;
use Tulia\Cms\Node\Domain\WriteModel\Service\SlugGeneratorStrategy\SlugGeneratorStrategyInterface;
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
        private string $websiteId,
        private string $author,
    ) {
    }

    public static function create(
        string $id,
        string $type,
        string $websiteId,
        string $author,
        string $title,
        array $availableLocales,
    ): self {
        $self = new self($id, $type, $websiteId, $author);
        $self->createdAt = new ImmutableDateTime();
        $self->publishedAt = new ImmutableDateTime();
        $self->translations = self::createTranslations($self, $title, $availableLocales);
        $self->purposes = new ArrayCollection();
        $self->recordThat(new Event\NodeCreated($id, $type));

        return $self;
    }

    public static function fromArray(array $data): self
    {
        $self = new self(
            $data['id'],
            $data['type'],
            $data['website_id'],
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

    /**
     * @param string[] $availableLocales
     * @return NodeTranslation[]|ArrayCollection<int, NodeTranslation>
     */
    private static function createTranslations(
        self $node,
        string $title,
        array $availableLocales,
    ): ArrayCollection {
        $translations = [];

        foreach ($availableLocales as $locale) {
            $translations[] = new NodeTranslation($node, $locale, $title, false);
        }

        return new ArrayCollection($translations);
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
            'website_id'    => $this->websiteId,
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

    public function isTranslatedTo(string $locale): bool
    {
        foreach ($this->translations as $translation) {
            if ($translation->isFor($locale) && $translation->isTranslated()) {
                return true;
            }
        }

        return false;
    }

    private function trans(string $locale): NodeTranslation
    {
        foreach ($this->translations as $translation) {
            if ($translation->isFor($locale)) {
                return $translation;
            }
        }

        throw NodeTranslationDoesntExists::fromLocale($this->id, $locale);
    }

    public function changeTitle(
        string $locale,
        string $defaultLocale,
        SlugGeneratorStrategyInterface $slugGeneratorStrategy,
        string $title,
        ?string $slug = null,
    ): void {
        $trans = $this->trans($locale);
        $trans->changeTitle($slugGeneratorStrategy, $this->websiteId, $title, $slug);
        $trans->translated = true;

        if ($locale === $defaultLocale) {
            foreach ($this->translations as $translation) {
                if (false === $translation->isTranslated()) {
                    $translation->changeTitle($slugGeneratorStrategy, $this->websiteId, $title, $slug);
                }
            }
        }

        $this->markAsUpdated();
    }

    public function persistAttributes(string $locale, string $defaultLocale, array $attributes): void
    {
        $trans = $this->trans($locale);
        $trans->persistAttributes(...$attributes);
        $trans->translated = true;

        if ($locale === $defaultLocale) {
            foreach ($this->translations as $translation) {
                if (false === $translation->isTranslated()) {
                    $translation->persistAttributes(...$attributes);
                }
            }
        }

        $this->markAsUpdated();
    }

    public function publish(ImmutableDateTime $publishedAt): void
    {
        $this->publishedAt = $publishedAt;
        $this->status = 'published';

        $this->recordThat(new Event\NodePublished($this->id, $this->type, $this->publishedAt));
        $this->markAsUpdated();
    }

    public function setStatus(string $status): void
    {
        $this->status = $status;
        $this->markAsUpdated();
    }

    public function moveAsChildOf(Node $parent/*, NodeLevelCalculatorInterface $calculator*/): void
    {
        $this->parent = $parent;
        /*$this->level = $calculator->calculate($this->id);*/
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

            $reason = $rules->decide($this->id, $this->websiteId, $purpose, ...$this->purposes);

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

        $reason = $rules->decide($this->id, $this->websiteId, $purpose, ...$this->purposes);

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

    public function delete(CanDeleteNodeInterface $rules): void
    {
        $reason = $rules->decide($this->id);

        if (CanDeleteNodeReasonEnum::OK !== $reason) {
            throw CannotDeleteNodeException::fromReason($reason, $this->id);
        }

        $this->recordThat(new Event\NodeDeleted($this->id, $this->type, $this->websiteId, $this->getTranslationsLocales()));
    }

    private function markAsUpdated(): void
    {
        $this->updatedAt = new ImmutableDateTime();
        $this->recordUniqueThat(new NodeUpdated($this->id, $this->type), function (NodeUpdated $event) {
            return $event->id === $this->id;
        });
    }

    private function getTranslationsLocales(): array
    {
        return array_map(
            static fn ($v) => $v->getLocale(),
            $this->translations->toArray()
        );
    }
}
