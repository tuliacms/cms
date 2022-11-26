<?php

declare(strict_types=1);

namespace Tulia\Component\Importer\Exception;

/**
 * @author Adam Banaszkiewicz
 */
final class ObjectImportFailedContextAwareException extends \Exception
{
    public static function fromContext(string $message): self
    {
        return new self($message);
    }
}
