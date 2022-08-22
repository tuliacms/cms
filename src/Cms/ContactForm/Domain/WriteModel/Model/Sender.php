<?php

declare(strict_types=1);

namespace Tulia\Cms\ContactForm\Domain\WriteModel\Model;

/**
 * @author Adam Banaszkiewicz
 * @final
 */
class Sender
{
    public function __construct(
        public string $email,
        public ?string $name = null
    ) {
    }
}
