<?php

declare(strict_types=1);

namespace Tulia\Cms\Menu\Domain\ReadModel\Finder;

/**
 * @author Adam Banaszkiewicz
 */
class MenuFinderScopeEnum
{
    /**
     * Backend, nodes listing.
     */
    public const BACKEND_LISTING = 'backend.listing';

    /**
     * Backend, single menu fetch, like edit/update/delete.
     */
    public const BACKEND_SINGLE = 'backend.single';

    public const BUILD_MENU = 'build_menu';

    public const INTERNAL = 'internal';
}
