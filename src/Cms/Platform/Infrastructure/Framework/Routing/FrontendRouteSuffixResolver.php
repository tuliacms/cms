<?php

declare(strict_types=1);

namespace Tulia\Cms\Platform\Infrastructure\Framework\Routing;

use Tulia\Cms\Options\Domain\ReadModel\Options;

/**
 * @author Adam Banaszkiewicz
 */
class FrontendRouteSuffixResolver
{
    private ?string $suffix = null;

    public function __construct(
        private readonly Options $options,
    ) {
    }

    public function appendSuffix(string $url): string
    {
        if ($this->suffixExists($url)) {
            return $url;
        }

        return $url . $this->getSuffix();
    }

    public function removeSuffix(string $url): string
    {
        if ($this->suffixExists($url) === false) {
            return $url;
        }

        return substr($url, 0, -\strlen($this->getSuffix()));
    }

    public function suffixExists(string $url): bool
    {
        return substr($url, -\strlen($this->getSuffix())) === $this->getSuffix();
    }

    public function getSuffix(): string
    {
        if ($this->suffix) {
            return $this->suffix;
        }

        return $this->suffix = (string) $this->options->get('url_suffix');
    }
}
