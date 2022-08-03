<?php

declare(strict_types=1);

namespace Tulia\Cms\Widget\Domain\Renderer;

use Tulia\Cms\Content\Attributes\Domain\ReadModel\Service\AttributesFinder;
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
        private StorageInterface $storage,
        private WidgetRegistryInterface $registry,
        private EngineInterface $engine,
        private AttributesFinder $attributeFinder,
    ) {
    }

    /**
     * {@inheritdoc}
     */
    public function forId(string $id, string $locale): string
    {
        $widget = $this->storage->findById($id, $locale);

        if ($widget === []) {
            return '';
        }

        $widget = $this->prepareWidgetData($widget, $locale);

        if (! $widget['visibility']) {
            return '';
        }

        return $this->render($widget);
    }

    /**
     * {@inheritdoc}
     */
    public function forSpace(string $space, string $locale): string
    {
        $widgets = $this->storage->findBySpace($space, $locale);

        if ($widgets === []) {
            return '';
        }

        $result = [];

        foreach ($widgets as $widget) {
            $widget = $this->prepareWidgetData($widget, $locale);

            if (! $widget['visibility']) {
                continue;
            }

            $result[] = $this->render($widget);
        }

        return implode('', $result);
    }

    private function render(array $data): string
    {
        if ($this->registry->has($data['widget_type']) === false) {
            return '';
        }

        $config = new ArrayConfiguration($data['space']);
        $widget = $this->registry->get($data['widget_type'])->getInstance();
        $widget->configure($config);
        //$config->merge($data['attributes']);

        $view = $widget->render($config);

        if (! $view) {
            return '';
        }

        $classes = 'widget-item';
        $classes .= ' widget-item-outer';
        $classes .= ' widget-space-' . $data['space'];
        $classes .= ' widget-item-' . $data['id'];
        $classes .= ' ' . implode(' ', $data['styles']);
        $classes .= ' widget-' . str_replace('.', '-', strtolower($data['widget_type']));

        if ($data['html_class']) {
            $classes .= ' ' . $data['html_class'];
        }

        $attributes = [
            'class' => $classes,
        ];

        if ($data['html_id']) {
            $attributes['id'] = $data['html_id'];
        }

        $view->addData([
            'config' => $config,
            'widgetTitle' => $data['title'],
            'widgetAttributes' => $attributes,
        ]);

        return $this->engine->render($view);
    }

    private function prepareWidgetData(array $widget, string $locale): array
    {
        $widget['visibility'] = (bool) $widget['visibility'];
        $widget['styles'] = (array) json_decode($widget['styles'], true);
        //$widget['attributes'] = $this->attributeFinder->findAll('widget', 'scope', $widget['id'], $locale);

        return $widget;
    }
}
