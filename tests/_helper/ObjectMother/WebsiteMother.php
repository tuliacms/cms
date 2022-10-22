<?php

declare(strict_types=1);

namespace Tulia\Cms\Tests\Helper\ObjectMother;

use Tulia\Cms\Website\Domain\WriteModel\Model\Website;
use Tulia\Cms\Website\Domain\WriteModel\Rules\CannAddLocale\CanAddLocale;

/**
 * @author Adam Banaszkiewicz
 */
final class WebsiteMother
{
    private string $name;
    private string $defaultLocale = 'en_US';
    private bool $active = true;
    private array $locales = [];

    private function __construct(string $name)
    {
        $this->name = $name;
    }

    public static function aWebsite(string $name): self
    {
        return new self($name);
    }

    public function withDefaultLocale(string $code): self
    {
        $this->defaultLocale = $code;
        return $this;
    }

    public function withLocale(string $code): self
    {
        $this->locales[] = $code;
        return $this;
    }

    public function isInactive(): self
    {
        $this->active = false;
        return $this;
    }

    public function build(): Website
    {
        $website = Website::create(
            id: $this->name,
            name: $this->name,
            localeCode: $this->defaultLocale,
            domain: 'localhost',
            active: $this->active,
        );

        if ([] !== $this->locales) {
            foreach ($this->locales as $locale) {
                $website->addLocale(new CanAddLocale(), $locale);
            }
        }

        $website->collectDomainEvents();

        return $website;
    }
}
