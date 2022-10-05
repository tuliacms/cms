<?php

declare(strict_types=1);

namespace Tulia\Component\Theme\Bridge\Hooks;

use Tulia\Component\Hooks\HooksSubscriberInterface;
use Tulia\Component\Theme\Customizer\DetectorInterface;
use Tulia\Component\Theme\ManagerInterface;

/**
 * @author Adam Banaszkiewicz
 * @final
 */
class VariablesStyleRegistrator implements HooksSubscriberInterface
{
    public function __construct(
        private readonly ManagerInterface $manager,
        private readonly DetectorInterface $detector,
    ) {
    }

    public static function getSubscribedActions(): array
    {
        return [
            'theme.head' => 'registerVariables',
        ];
    }

    public function registerVariables(): string
    {
        $theme = $this->manager->getTheme();
        $config = $theme->getConfig();
        $source = $config->getVariables();
        $variables = [];

        foreach ($source as $selector => $vars) {
            foreach ($vars as $variable => $configKey) {
                $variables[$selector][$variable] = $config->getCustomizerVariable($configKey);
                $source[$selector][$variable] = [
                    'key' => $configKey,
                    'value' => $variables[$selector][$variable],
                ];
            }
        }

        return $this->buildStyleWithVariables($variables, $source);
    }

    private function buildStyleWithVariables(array $variables, array $source): string
    {
        $result = '<style id="theme-configuration-variables">';

        foreach ($variables as $selector => $vars) {
            $result .= sprintf('%s{', $selector);
            foreach ($vars as $variable => $value) {
                $result .= sprintf('--%s:%s;', $variable, $value);
            }
            $result .= '}';
        }

        $result .= '</style>';

        if ($this->detector->isCustomizerMode()) {
            $result .= PHP_EOL.sprintf('<script type="text/template" id="tulia-theme-configuration-variables">%s</script>', json_encode($source));
        }

        return $result;
    }
}
