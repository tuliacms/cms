<?php

declare(strict_types=1);

namespace Tulia\Cms\Widget\Domain\Renderer;

use Tulia\Cms\Platform\Infrastructure\Framework\Theme\ThemeViewOverwriteProducer;
use Tulia\Cms\Widget\Domain\Catalog\Configuration\ArrayConfiguration;
use Tulia\Cms\Widget\Domain\Catalog\Registry\WidgetRegistryInterface;
use Tulia\Cms\Widget\Domain\Catalog\Storage\StorageInterface;
use Tulia\Component\Templating\EngineInterface;

/**
 * @author Adam Banaszkiewicz
 */
class Renderer implements RendererInterface
{
    public function __construct(
        private readonly StorageInterface $storage,
        private readonly WidgetRegistryInterface $registry,
        private readonly EngineInterface $engine,
        private readonly ThemeViewOverwriteProducer $overwriteProducer,
    ) {
    }

    public function forId(string $id, string $websiteId, string $locale): string
    {
        $widget = $this->storage->findById($id, $websiteId, $locale);

        if ($widget === []) {
            return '';
        }

        $widget = $this->prepareWidgetData($widget);

        if (! $widget['visibility']) {
            return '';
        }

        return $this->render($widget, $websiteId, $locale);
    }

    public function forSpace(string $space, string $websiteId, string $locale): string
    {
        $widgets = $this->storage->findBySpace($space, $websiteId, $locale);

        if ($widgets === []) {
            return '';
        }

        $result = [];

        foreach ($widgets as $widget) {
            $widget = $this->prepareWidgetData($widget);

            if (! $widget['visibility']) {
                continue;
            }

            $result[] = $this->render($widget, $websiteId, $locale);
        }

        return implode('', $result);
    }

    private function render(array $data, string $websiteId, string $locale): string
    {
        if ($this->registry->has($data['type']) === false) {
            return '';
        }

        $config = new ArrayConfiguration($data['space']);
        $widget = $this->registry->get($data['type'])->getInstance();
        $widget->configure($config);
        $config->set('locale', $locale);
        $config->set('website_id', $websiteId);
        $config->merge($data['attributes']);

        $widgetView = $widget->render($config);

        if (! $widgetView) {
            return '';
        }

        $widgetView->setViews($this->overwriteProducer->produce('widget', $widgetView->getViews()));

        $classes = 'widget-item';
        $classes .= ' widget-item-outer';
        $classes .= ' widget-space-' . $data['space'];
        $classes .= ' widget-item-' . $data['id'];
        $classes .= ' ' . implode(' ', $data['styles']);
        $classes .= ' widget-' . str_replace('.', '-', strtolower($data['type']));

        if ($data['html_class']) {
            $classes .= ' ' . $data['html_class'];
        }

        $attributes = [
            'class' => $classes,
        ];

        if ($data['html_id']) {
            $attributes['id'] = $data['html_id'];
        }

        $widgetView->addData([
            'config' => $config,
            'widgetTitle' => $data['title'],
            'widgetAttributes' => $attributes,
        ]);

        return $this->engine->render($widgetView);
    }

    private function prepareWidgetData(array $widget): array
    {
        $widget['visibility'] = (bool) $widget['visibility'];
        $widget['styles'] = (array) json_decode($widget['styles'], true);

        return $widget;
    }
}
