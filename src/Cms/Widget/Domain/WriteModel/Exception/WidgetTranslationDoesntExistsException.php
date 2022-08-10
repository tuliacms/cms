<?php

declare(strict_types=1);

namespace Tulia\Cms\Widget\Domain\WriteModel\Exception;

use Tulia\Cms\Shared\Domain\WriteModel\Exception\AbstractDomainException;

/**
 * @author Adam Banaszkiewicz
 */
final class WidgetTranslationDoesntExistsException extends AbstractDomainException
{
    public static function fromLocale(string $id, string $locale): self
    {
        return new self(sprintf('Translation to %s for widget %s does not exists.', $locale, $id));
    }
}
