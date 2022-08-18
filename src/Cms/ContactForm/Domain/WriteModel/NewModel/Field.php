<?php

declare(strict_types=1);

namespace Tulia\Cms\ContactForm\Domain\WriteModel\NewModel;

/**
 * @author Adam Banaszkiewicz
 * @final
 */
class Field
{
    private string $id;

    public function __construct(
        private FormTranslation $translation,
        private string $name,
        private string $type,
        private string $typeAlias
    ) {
    }
}
