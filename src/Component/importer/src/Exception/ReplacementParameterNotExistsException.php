<?php

declare(strict_types=1);

namespace Tulia\Component\Importer\Exception;

/**
 * @author Adam Banaszkiewicz
 */
final class ReplacementParameterNotExistsException extends \Exception
{
    public static function fromName(string $parameter): self
    {
        return new self(sprintf('Parameter "%s" is not defined in system.', $parameter));
    }
}
