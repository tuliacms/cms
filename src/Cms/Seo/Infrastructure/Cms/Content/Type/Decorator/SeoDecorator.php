<?php

declare(strict_types=1);

namespace Tulia\Cms\Seo\Infrastructure\Cms\Content\Type\Decorator;

use Tulia\Cms\Content\Type\Domain\ReadModel\ContentTypeBuilder\ContentTypeCollector;
use Tulia\Cms\Content\Type\Domain\ReadModel\ContentTypeBuilder\Definition\ContentTypeDefinition;
use Tulia\Cms\Content\Type\Domain\ReadModel\Service\ContentTypeDecoratorInterface;

/**
 * @author Adam Banaszkiewicz
 */
class SeoDecorator implements ContentTypeDecoratorInterface
{
    public function __construct(
        private readonly array $robotsOptionsList,
    ) {
    }

    public function decorate(ContentTypeCollector $collector): void
    {
        foreach ($collector->all() as $type) {
            if ($type->isRoutable === false) {
                continue;
            }

            $this->decorateDefinition($type);
        }
    }

    private function decorateDefinition(ContentTypeDefinition $definition): void
    {
        $group = $definition->fieldsGroup('seo', 'SEO', 'main');
        $group->field('seo_title', 'text', 'title', ['mutlilingual' => true, 'configuration' => ['help' => 'Fill to overwrite default title'], 'translation_domain' => 'seo']);
        $group->field('seo_description', 'text', 'metaDescription', ['mutlilingual' => true, 'translation_domain' => 'seo']);
        $group->field('seo_keywords', 'text', 'metaKeywords', ['mutlilingual' => true, 'translation_domain' => 'seo']);
        $group->field('seo_og_image', 'filepicker', 'metaOgImage', ['translation_domain' => 'seo']);

        $group->field('seo_robots', 'multiselect', 'metaRobots', [
            'translation_domain' => 'seo',
            'configuration' => [
                'multiple' => true,
                'choices' => $this->robotsOptionsList,
            ]
        ]);
    }
}
