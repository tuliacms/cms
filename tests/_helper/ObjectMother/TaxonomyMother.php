<?php

declare(strict_types=1);

namespace Tulia\Cms\Tests\Helper\ObjectMother;

use Symfony\Component\Uid\Uuid;
use Tulia\Cms\Taxonomy\Domain\WriteModel\Model\Taxonomy;
use Tulia\Cms\Tests\Helper\TestDoubles\Shared\Domain\WriteModel\Service\SlugGeneratorStrategy\PassthroughSluggeneratorStrategy;

/**
 * @author Adam Banaszkiewicz
 */
final class TaxonomyMother
{
    private string $type;
    private string $websiteId = 'ceb7a799-9491-4880-a50e-0c7f0dcba7f5';
    private string $locale = 'en_US';
    private string $defaultLocale = 'en_US';
    private array $locales = ['en_US'];
    private array $terms = [];
    private array $termsTranslations = [];

    private function __construct(string $type)
    {
        $this->type = $type;
    }

    public static function aTaxonomy(string $type): self
    {
        return new self($type);
    }

    public function withTerm(string $term): self
    {
        $this->terms[] = $term;
        return $this;
    }

    public function withTermTranslatedTo(string $term, string $locale, string $name): self
    {
        $this->termsTranslations[$term][$locale] = $name;
        return $this;
    }

    public function withLocales(array $locales): self
    {
        $this->locales = $locales;
        return $this;
    }

    public function build(): Taxonomy
    {
        $slugStrategy = new PassthroughSluggeneratorStrategy();

        $taxonomy = Taxonomy::create(
            $this->type,
            $this->websiteId,
            $this->locales,
            $this->locale,
        );

        foreach ($this->terms as $term) {
            $taxonomy->addTerm(
                $slugStrategy,
                $term,
                $term,
                $term,
                $this->locales,
                $this->locale,
                $this->defaultLocale,
            );

            if (isset($this->termsTranslations[$term])) {
                foreach ($this->termsTranslations[$term] as $locale => $translation) {
                    $taxonomy->updateTerm($slugStrategy, $term, $locale, $translation, null, $this->defaultLocale);
                }
            }
        }

        $taxonomy->collectDomainEvents();

        return $taxonomy;
    }
}
