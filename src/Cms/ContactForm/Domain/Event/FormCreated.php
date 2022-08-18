<?php

declare(strict_types=1);

namespace Tulia\Cms\ContactForm\Domain\Event;

use Tulia\Cms\Shared\Domain\WriteModel\Event\AbstractDomainEvent;

/**
 * @author Adam Banaszkiewicz
 */
class FormCreated extends AbstractDomainEvent
{
    private string $id;
    private string $locale;

    public function __construct(string $id, string $locale)
    {
        $this->id = $id;
        $this->locale = $locale;
    }

    public function getLocale(): string
    {
        return $this->locale;
    }
}
