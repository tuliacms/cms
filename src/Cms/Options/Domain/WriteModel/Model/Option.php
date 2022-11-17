<?php

declare(strict_types=1);

namespace Tulia\Cms\Options\Domain\WriteModel\Model;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Tulia\Cms\Shared\Domain\WriteModel\Model\AbstractAggregateRoot;

/**
 * @author Adam Banaszkiewicz
 * @final
 */
class Option extends AbstractAggregateRoot
{
    private string $id;
    /** @var ArrayCollection<int, OptionTranslation> */
    private Collection $translations;

    public function __construct(
        private string $name,
        private mixed $value,
        private string $websiteId,
        private bool $multilingual = false,
        private bool $autoload = false
    ) {
        $this->translations = new ArrayCollection();
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getValue(string $locale): mixed
    {
        foreach ($this->translations as $pretendent) {
            if ($pretendent->locale === $locale) {
                return $pretendent->value;
            }
        }

        return $this->value;
    }

    public function setValue(mixed $value, ?string $locale = null, ?string $defaultLocale = null): void
    {
        if ($this->multilingual) {
            \assert(!empty($locale), 'Please provide $locale for this multilingual option');
            \assert(!empty($defaultLocale), 'Please provide defaultLocale for this multilingual option');

            if ($locale === $defaultLocale) {
                $this->value = $value;
            }

            $translation = null;

            foreach ($this->translations as $pretendent) {
                if ($pretendent->locale === $locale) {
                    $translation = $pretendent;
                }
            }

            if (!$translation) {
                $translation = new OptionTranslation($this, $locale, $value);
                $this->translations->add($translation);
            }

            $translation->value = $value;
        } else {
            $this->value = $value;
        }
    }
}
