<?php

declare(strict_types=1);

namespace Tulia\Cms\Platform\UserInterface\Web\DashboardWidget;

use Tulia\Cms\Homepage\UserInterface\Web\Backend\Widgets\AbstractDashboardWidget;

/**
 * @author Adam Banaszkiewicz
 */
class SystemUpdatesWidget extends AbstractDashboardWidget
{
    public function render(): string
    {
        return $this->view('@backend/dashboard-widgets/system-updates.tpl');
    }

    public function supports(string $group): bool
    {
        return $group === 'backend.dashboard';
    }
}
