<?php

declare(strict_types=1);

namespace Tulia\Cms\BackendMenu\Builder;

/**
 * @author Adam Banaszkiewicz
 */
interface HtmlBuilderInterface
{
    public function build(string $websiteId, string $locale, array $params = []): string;
}
