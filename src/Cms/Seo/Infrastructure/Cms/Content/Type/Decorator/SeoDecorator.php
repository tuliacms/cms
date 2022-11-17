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
        $group = $definition->fieldsGroup('seo', 'main', 'SEO');
        $group->field('seo_title', 'text', 'title', ['mutlilingual' => true, 'configuration' => ['help' => 'Fill to overwrite default title'], 'translation_domain' => 'seo']);
        $group->field('seo_description', 'text', 'metaDescription', ['mutlilingual' => true, 'translation_domain' => 'seo']);
        $group->field('seo_keywords', 'text', 'metaKeywords', ['mutlilingual' => true, 'translation_domain' => 'seo']);
        $group->field('seo_og_image', 'filepicker', 'metaOgImage', ['translation_domain' => 'seo']);

        $group->field('seo_robots', 'multiselect', 'metaRobots', [
            'configuration' => [
                'multiple' => true,
                'choices' => [
                    'all' => 'all',
                    'noindex' => 'noindex',
                    'nofollow' => 'nofollow',
                    'none' => 'none',
                    'noarchive' => 'noarchive',
                    'nositelinkssearchbox' => 'nositelinkssearchbox',
                    'nosnippet' => 'nosnippet',
                    'indexifembedded' => 'indexifembedded',
                    'notranslate' => 'notranslate',
                    'noimageindex' => 'noimageindex',
                ]
            ]
        ]);
    }
}
