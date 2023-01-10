<?php

declare(strict_types=1);

namespace Tulia\Component\Theme\Bridge\Twig\Extension;

use Tulia\Component\Theme\Customizer\DetectorInterface;
use Tulia\Component\Theme\ManagerInterface;
use Tulia\Component\Theme\ThemeInterface;
use Tulia\Component\Theme\ThemeTemplateProviderInterface;
use Twig\Extension\RuntimeExtensionInterface;

/**
 * @author Adam Banaszkiewicz
 */
class ThemeRuntime implements RuntimeExtensionInterface
{
    public function __construct(
        private readonly ManagerInterface $manager,
        private readonly DetectorInterface $detector,
        private readonly ThemeTemplateProviderInterface $templateProvider,
    ) {
    }

    public function theme(): ThemeInterface
    {
        return $this->manager->getTheme();
    }

    public function themeTemplate(string $template): string
    {
        return $this->templateProvider->template($template);
    }

    public function parentThemeTemplate(string $template): string
    {
        return $this->templateProvider->parentTemplate($template);
    }

    public function customizerGet(string $name, mixed $default = null): mixed
    {
        return $this->manager->getTheme()->getConfig()->getCustomizerVariable($name, $default);
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
            $options['control'] = $name;

            return ' data-tulia-customizer-live-control=\''.json_encode($options).'\'';
        }

        return '';
    }
}
