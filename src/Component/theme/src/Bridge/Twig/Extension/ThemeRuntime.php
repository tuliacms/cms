<?php

declare(strict_types=1);

namespace Tulia\Component\Theme\Bridge\Twig\Extension;

use Requtize\Assetter\AssetterInterface;
use Tulia\Cms\Platform\Version;
use Tulia\Component\Theme\Customizer\DetectorInterface;
use Tulia\Component\Theme\ManagerInterface;
use Tulia\Component\Theme\ThemeInterface;
use Twig\Extension\RuntimeExtensionInterface;

/**
 * @author Adam Banaszkiewicz
 */
class ThemeRuntime implements RuntimeExtensionInterface
{
    public function __construct(
        private ManagerInterface $manager,
        private AssetterInterface $assetter,
        private DetectorInterface $detector,
    ) {
    }

    public function theme(): ThemeInterface
    {
        return $this->manager->getTheme();
    }

    /**
     * @param mixed $default
     * @return mixed
     */
    public function customizerGet(string $name, $default = null)
    {
        return $this->manager->getTheme()->getConfig()->get('customizer', $name, $default);
    }

    public function customizerLiveControl(string $name, array $options = []): string
    {
        if ($this->detector->isCustomizerMode()) {
            $options = array_merge([
                'hide_empty' => true,
                'nl2br' => false,
                'type' => 'inner-text',
                'default' => null,
            ], $options);

            return ' data-tulia-customizer-live-control=\'{"control":"'.$name.'","nl2br":"'.($options['nl2br'] ? 'true' : 'false').'","type":"'.$options['type'].'","default":"'.$options['default'].'"}\'';
        }

        return '';
    }

    public function themeHead(): string
    {
        return $this->assetter->build('head')->all().PHP_EOL.'<meta name="generator" content="TuliaCMS '.Version::VERSION.'" />';
    }

    public function themeBody(): string
    {
        return $this->assetter->build()->all();
    }
}
