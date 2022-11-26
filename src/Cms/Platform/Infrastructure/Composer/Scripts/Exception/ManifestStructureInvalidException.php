<?php

declare(strict_types=1);

namespace Tulia\Cms\Platform\Infrastructure\Composer\Scripts\Exception;

/**
 * @author Adam Banaszkiewicz
 */
final class ManifestStructureInvalidException extends \Exception
{
    public static function missingField(string $field): self
    {
        return new self(sprintf('Missing field %s in manifest file', $field));
    }

    public static function invalidValue(string $field, mixed $actual, mixed $expected): self
    {
        if (is_array($expected)) {
            $expected = implode(', ', $expected);
        }

        return new self(sprintf('Field %s has invalid value. Expected %s, but given %s', $field, $expected, $actual));
    }
}
