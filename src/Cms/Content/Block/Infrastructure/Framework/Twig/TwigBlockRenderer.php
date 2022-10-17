<?php

declare(strict_types=1);

namespace Tulia\Cms\Content\Block\Infrastructure\Framework\Twig;

use Tulia\Cms\Content\Attributes\Domain\ReadModel\Model\AttributeValue;
use Tulia\Cms\Content\Block\Domain\Renderer\BlockRendererInterface;
use Tulia\Cms\Content\Type\Domain\ReadModel\Service\ContentTypeRegistryInterface;
use Tulia\Component\Templating\EngineInterface;
use Tulia\Component\Templating\View;

/**
 * @author Adam Banaszkiewicz
 */
final class TwigBlockRenderer implements BlockRendererInterface
{
    private string $fallbackView;

    public function __construct(
        private readonly ContentTypeRegistryInterface $contentTypeRegistry,
        private readonly EngineInterface $engine,
        private readonly string $environment,
        private array $paths,
    ) {
        $this->prepareViews();
    }

    public function render(array $model): string
    {
        $result = [];

        foreach ($model['blocks'] ?? [] as $block) {
            $result[] = $this->renderBlock($block);
        }

        return implode('', $result);
    }

    public function renderBlock(array $block): string
    {
        if (!$block['visible']) {
            return '';
        }

        $fields = ['__block' => $block];

        foreach ($block['fields'] as $name => $values) {
            $fields[$name] = new AttributeValue($values);
        }

        $views = array_map(static function (string $path) use ($block) {
            return $path . $block['type'] . '.tpl';
        }, $this->paths);

        $views[] = $this->fallbackView;

        return $this->engine->render(
            new View($views, $fields)
        );
    }

    private function prepareViews(): void
    {
        if ($this->environment === 'dev') {
            $this->fallbackView = '@cms/content_block/empty-block.debug.tpl';
        } else {
            $this->fallbackView = '@cms/content_block/empty-block.tpl';
        }

        /**
         * Views priority:
         * - Theme views - This allows to overwrite views from modules
         * - Modules views
         * - Fallback views - At the end, we have to add empty view, in case of any previous views are not defined.
         * @todo Create absolute path to active theme instead of `@theme` namespace
         */
        array_unshift($this->paths, '@theme/content-block/');
    }
}
