<?php

declare(strict_types=1);

namespace Tulia\Cms\Widget\Domain\WriteModel\Model;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Tulia\Cms\Shared\Domain\WriteModel\Model\AbstractAggregateRoot;
use Tulia\Cms\Widget\Domain\WriteModel\Event;
use Tulia\Cms\Widget\Domain\WriteModel\Exception\WidgetTranslationDoesntExistsException;

/**
 * @author Adam Banaszkiewicz
 * @final
 */
class Widget extends AbstractAggregateRoot
{
    private ?string $htmlClass = null;
    private ?string $htmlId = null;
    private array $styles = [];
    /** @var WidgetTranslation[]|ArrayCollection<int, WidgetTranslation> */
    private Collection $translations;

    private function __construct(
        private string $id,
        private string $websiteId,
        private string $type,
        private string $space,
        private string $name,
        string $creatingLocale,
        array $localeCodes,
    ) {
        $translations = [];

        foreach ($localeCodes as $locale) {
            $translations[] = new WidgetTranslation($this, $locale, $creatingLocale);
        }

        $this->translations = new ArrayCollection($translations);
    }

    public static function create(
        string $id,
        string $websiteId,
        string $type,
        string $space,
        string $name,
        string $creatingLocale,
        array $localeCodes,
    ): self {
        $self = new self($id, $websiteId, $type, $space, $name, $creatingLocale, $localeCodes);
        $self->recordThat(new Event\WidgetCreated($self->id, $self->type));

        return $self;
    }

    public function getId(): string
    {
        return $this->id;
    }

    public function getType(): string
    {
        return $this->type;
    }

    public function toArray(string $locale, string $defaultLocale): array
    {
        $translation = $this->translation($locale, $defaultLocale)->toArray();

        return [
            'htmlClass' => $this->htmlClass,
            'htmlId' => $this->htmlId,
            'styles' => $this->styles,
            'id' => $this->id,
            'type' => $this->type,
            'space' => $this->space,
            'name' => $this->name,
            'title' => $translation['title'],
            'visibility' => $translation['visibility'],
            'attributes' => $translation['attributes'],
        ];
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

    public function turnVisibilityOn(string $locale, string $defaultLocale): void
    {
        $trans = $this->translation($locale, $defaultLocale);
        $trans->visibility = true;

        if ($locale === $defaultLocale) {
            foreach ($this->translations as $translation) {
                if (false === $translation->isTranslated()) {
                    $translation->visibility = true;
                }
            }
        } else {
            $trans->translated = true;
        }
    }

    public function turnVisibilityOff(string $locale, string $defaultLocale): void
    {
        $trans = $this->translation($locale, $defaultLocale);
        $trans->visibility = false;
        $trans->translated = true;

        if ($locale === $defaultLocale) {
            foreach ($this->translations as $translation) {
                if (false === $translation->isTranslated()) {
                    $translation->visibility = false;
                }
            }
        }
    }

    public function rename(string $name): void
    {
        $this->name = $name;
    }

    public function setHtmlId(?string $htmlId): void
    {
        $this->htmlId = $htmlId;
    }

    public function setHtmlClass(?string $htmlClass): void
    {
        $this->htmlClass = $htmlClass;
    }

    public function updateStyles(array $styles): void
    {
        $this->styles = $styles;
    }

    public function changeTitle(string $locale, string $defaultLocale, ?string $title): void
    {
        $trans = $this->translation($locale, $defaultLocale);

        if ($title === $trans->title) {
            return;
        }

        $trans->title = $title;
        $trans->translated = true;

        if ($locale === $defaultLocale) {
            foreach ($this->translations as $translation) {
                if (false === $translation->isTranslated()) {
                    $translation->title = $title;
                }
            }
        }
    }

    public function delete(): void
    {
    }

    public function persistAttributes(string $locale, string $defaultLocale, array $attributes): void
    {
        $trans = $this->translation($locale, $defaultLocale);
        $trans->persistAttributes(...$attributes);
        $trans->translated = true;

        if ($locale === $defaultLocale) {
            foreach ($this->translations as $translation) {
                if (false === $translation->isTranslated()) {
                    $translation->persistAttributes(...$attributes);
                }
            }
        }
    }

    private function translation(string $locale, string $defaultLocale): WidgetTranslation
    {
        if ($this->hasTranslation($locale)) {
            $translation = $this->trans($locale);
        } else {
            $translation = WidgetTranslation::cloneToLocale(
                $this->trans($defaultLocale),
                $locale
            );

            $this->translations->add($translation);
        }

        /*$translation->setUpdateCallback(function () {
            $this->markAsUpdated();
        });*/

        return $translation;
    }

    private function trans(string $locale): WidgetTranslation
    {
        foreach ($this->translations as $translation) {
            if ($translation->isFor($locale)) {
                /*$translation->setUpdateCallback(function () {
                    $this->markAsUpdated();
                });*/
                return $translation;
            }
        }

        throw WidgetTranslationDoesntExistsException::fromLocale($this->id, $locale);
    }

    private function hasTranslation(string $locale): bool
    {
        foreach ($this->translations as $translation) {
            if ($translation->isFor($locale)) {
                return true;
            }
        }

        return false;
    }
}
