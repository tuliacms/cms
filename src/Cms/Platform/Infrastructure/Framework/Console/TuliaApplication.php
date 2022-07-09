<?php

declare(strict_types=1);

namespace Tulia\Cms\Platform\Infrastructure\Framework\Console;

use Symfony\Bundle\FrameworkBundle\Console\Application;
use Symfony\Component\Console\Input\InputDefinition;
use Symfony\Component\Console\Input\InputOption;

/**
 * @author Adam Banaszkiewicz
 */
final class TuliaApplication extends Application
{
    protected function getDefaultInputDefinition(): InputDefinition
    {
        $definition = parent::getDefaultInputDefinition();
        $definition->addOption(new InputOption('locale', 'l', InputOption::VALUE_OPTIONAL, 'Locale of the website. Leave empty to execute with default one.'));

        return $definition;
    }
}
