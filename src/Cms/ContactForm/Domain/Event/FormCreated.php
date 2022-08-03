<?php

declare(strict_types=1);

namespace Tulia\Cms\ContactForm\Domain\Event;

/**
 * @author Adam Banaszkiewicz
 */
class FormCreated extends AbstractDomainEvent
{
    private string $locale;

    public function __construct(string $id, string $locale)
    {
        parent::__construct($id);

        $this->locale = $locale;
    }

    public function getLocale(): string
    {
        return $this->locale;
    }
}
