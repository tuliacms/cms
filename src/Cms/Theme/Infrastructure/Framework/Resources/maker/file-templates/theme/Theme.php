<?php

declare(strict_types=1);

namespace Tulia\Theme\{{ theme.vendor }}\{{ theme.code }};

use Tulia\Component\Theme\AbstractTheme;

class Theme extends AbstractTheme
{
    protected $thumbnail = '/assets/theme/{{ theme.name.lc }}/theme/images/thumbnail.jpg';
    protected $parent = {{ theme.parent }};
    protected $info = 'Theme info';
    protected $description = 'Theme description';
}
