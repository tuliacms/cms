<?php

declare(strict_types=1);

namespace Tulia\Cms\Menu\Domain\WriteModel\Exception;

use Tulia\Cms\Shared\Domain\WriteModel\Exception\AbstractDomainException;

/**
 * @author Adam Banaszkiewicz
 */
final class ItemTranslationDoesntExistsException extends AbstractDomainException
{
    public static function fromLocale(string $id, string $locale): self
    {
        return new self(sprintf('Translation for locale %s does not exists in menu item %s', $locale, $id));
    }
}
