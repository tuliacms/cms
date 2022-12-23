<?php

declare(strict_types=1);

namespace Tulia\Cms\Tests\Behat\Taxonomy;

use Tulia\Cms\Taxonomy\Domain\WriteModel\Model\Taxonomy;
use Tulia\Cms\Tests\Behat\AggregateRootSpy;
use Tulia\Cms\Tests\Helper\ObjectMother\TaxonomyMother;

/**
 * @property Taxonomy $taxonomy
 * @property AggregateRootSpy $taxonomySpy
 * @author Adam Banaszkiewicz
 */
trait TaxonomyBuildableTrait
{
    private array $locales = ['en_US'];
    private TaxonomyMother $taxonomyBuilder;

    public function __get(string $name)
    {
        if ($name !== 'taxonomy') {
            throw new \LogicException(sprintf('You can get only Taxonomy through magic getter, "%s" called.', $name));
        }

        if (false === property_exists($this, 'taxonomy')) {
            $this->taxonomyBuilder->withLocales($this->locales);

            $this->taxonomy = $this->taxonomyBuilder->build();
            $this->taxonomySpy = new AggregateRootSpy($this->taxonomy);
        }

        return $this->taxonomy;
    }

    /**
     * @Given there is a taxonomy :type
     */
    public function thereIsATaxonomy(string $type): void
    {
        $this->taxonomyBuilder = TaxonomyMother::aTaxonomy($type);
    }

    /**
     * @Given which has term :term
     */
    public function whichHasTerm(string $term): void
    {
        $this->taxonomyBuilder->withTerm($term);
    }

    /**
     * @Given which has term :term translated to :locale with name :name
     */
    public function whichHasTermTranslatedToWithName(string $term, string $locale, string $name): void
    {
        $this->taxonomyBuilder->withTermTranslatedTo($term, $locale, $name);
    }

    /**
     * @Given there are available locales :locales
     */
    public function thereAreAvailableLocales(string $locales): void
    {
        $this->locales = explode(',', $locales);
    }
}
