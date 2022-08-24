<?php

declare(strict_types=1);

namespace Tulia\Component\Importer\Parameters;

use Tulia\Component\Importer\Exception\ReplacementParameterNotExistsException;

/**
 * @author Adam Banaszkiewicz
 */
final class ParametersCompiler
{
    private const PATTERN = '#\[\[%([a-z0-9\.\-\_]+)%]]#is';

    public function __construct(
        private readonly ParametersProviderInterface $parametersProvider
    ) {
    }

    /**
     * @throws ReplacementParameterNotExistsException
     */
    public function compileParametersInValues(array $data): array
    {
        return $this->replaceCollection($data, $this->parametersProvider->provide());
    }

    /**
     * @throws ReplacementParameterNotExistsException
     */
    private function replaceCollection(array $data, array $parameters): array
    {
        foreach ($data as $key => $val) {
            if (is_array($val)) {
                $data[$key] = $this->replaceCollection($val, $parameters);
            } elseif (is_string($val)) {
                $data[$key] = $this->replaceSingle($val, $parameters);
            }
        }

        return $data;
    }

    /**
     * @throws ReplacementParameterNotExistsException
     */
    private function replaceSingle(string $value, array $parameters): string
    {
        if (!preg_match(self::PATTERN, $value, $matches)) {
            return $value;
        }

        if (!isset($parameters[$matches[1]])) {
            throw ReplacementParameterNotExistsException::fromName($matches[1]);
        }

        return str_replace($matches[0], $parameters[$matches[1]], $value);
    }
}
