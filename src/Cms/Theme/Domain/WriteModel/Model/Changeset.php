<?php

declare(strict_types=1);

namespace Tulia\Cms\Theme\Domain\WriteModel\Model;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Tulia\Cms\Shared\Domain\WriteModel\Model\ValueObject\ImmutableDateTime;

/**
 * @author Adam Banaszkiewicz
 * @final
 */
class Changeset
{
    private const ACTIVE = 'active';
    private const TEMPORARY = 'temporary';

    /** @var Collection|ArrayCollection<int, ChangesetTranslation> */
    private Collection $translations;
    private ImmutableDateTime $updatedAt;
    private array $payload = [];

    private function __construct(
        private ?ThemeCustomization $themeCustomization,
        private string $id,
        private readonly string $theme,
        private readonly string $type = self::TEMPORARY,
    ) {
        $this->translations = new ArrayCollection();
        $this->updatedAt = ImmutableDateTime::now();
    }

    public static function create(
        ThemeCustomization $themeCustomization,
        string $id,
        string $theme,
        array $availableLocales,
        array $payload,
        array $multilingualOptions,
    ): self {
        $self = new self($themeCustomization, $id, $theme);

        [$self->payload, $multilingualPayload] = $self->separatePayload($payload, $multilingualOptions);

        foreach ($availableLocales as $locale) {
            $self->translations->add(new ChangesetTranslation($self, $locale, $multilingualPayload));
        }

        return $self;
    }

    public function getId(): string
    {
        return $this->id;
    }

    public function detach(): void
    {
        $this->themeCustomization = null;
    }

    public function isActive(): bool
    {
        return $this->type === self::ACTIVE;
    }

    public function isUpdatedBefore(ImmutableDateTime $dateTime): bool
    {
        return $this->updatedAt->isBefore($dateTime);
    }

    public function activate(): void
    {
        $this->type = self::ACTIVE;
        $this->updatedAt = ImmutableDateTime::now();
    }

    public function copy(string $id): self
    {
        $newOne = new self($this->themeCustomization, $id, $this->theme);
        $newOne->payload = $this->payload;

        foreach ($this->translations as $translation) {
            $newOne->translations->add($translation->copy());
        }

        return $newOne;
    }

    public function updatePayload(string $locale, array $payload, array $multilingualOptions): void
    {
        [$this->payload, $multilingualPayload] = $this->separatePayload($payload, $multilingualOptions);

        foreach ($this->translations as $translation) {
            if ($translation->isFor($locale)) {
                $translation->updatePayload($multilingualPayload);
            }
        }

        $this->updatedAt = ImmutableDateTime::now();
    }

    private function separatePayload(array $payload, array $multilingualOptions): array
    {
        $globalPayload = [];
        $multilingualPayload = [];

        foreach ($payload as $key => $val) {
            if (\in_array($key, $multilingualOptions, true)) {
                $multilingualPayload[$key] = $val;
            } else {
                $globalPayload[$key] = $val;
            }
        }

        return [$globalPayload, $multilingualPayload];
    }
}
