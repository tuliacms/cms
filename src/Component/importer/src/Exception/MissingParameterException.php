<?php

declare(strict_types=1);

namespace Tulia\Component\Importer\Exception;

/**
 * @author Adam Banaszkiewicz
 */
final class MissingParameterException extends \Exception
{
    public static function fromName(string $name): self
    {
        return new self(sprintf('Missing parameter "%s", which is expected during import.', $name));
    }
}
