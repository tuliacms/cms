<?php

declare(strict_types=1);

namespace Tulia\Cms\Website\Domain\WriteModel\Query;

/**
 * @author Adam Banaszkiewicz
 */
interface CurrentWebsiteProviderInterface
{
    public function getId(): string;
    public function getLocale(): string;
}
