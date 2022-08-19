<?php

declare(strict_types=1);

namespace Tulia\Cms\ContactForm\Domain\ReadModel\Query;

/**
 * @author Adam Banaszkiewicz
 */
interface AvailableContactFormsQueryInterface
{
    public function list(): array;
}
