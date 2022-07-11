<?php

declare(strict_types=1);

namespace Tulia\Cms\Node\Domain\WriteModel\Exception;

use Tulia\Cms\Shared\Domain\WriteModel\Exception\AbstractDomainException;

/**
 * @author Adam Banaszkiewicz
 */
final class NodeTranslationDoesntExists extends AbstractDomainException
{
    public static function fromLocale(string $id, string $locale): self
    {
        return new self(sprintf('Translation in "%s" locale does not exisist in "%s" node', $locale, $id));
    }
}
