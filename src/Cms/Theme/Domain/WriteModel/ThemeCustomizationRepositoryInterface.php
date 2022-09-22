<?php

declare(strict_types=1);

namespace Tulia\Cms\Theme\Domain\WriteModel;

use Tulia\Cms\Theme\Domain\WriteModel\Model\ThemeCustomization;

/**
 * @author Adam Banaszkiewicz
 */
interface ThemeCustomizationRepositoryInterface
{
    public function get(string $theme, string $websiteId): ThemeCustomization;

    public function save(ThemeCustomization $customization): void;
}
