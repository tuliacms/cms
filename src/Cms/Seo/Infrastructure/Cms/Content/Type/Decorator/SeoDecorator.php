<?php

declare(strict_types=1);

namespace Tulia\Cms\Seo\Infrastructure\Cms\Content\Type\Decorator;

use Tulia\Cms\Content\Type\Domain\ReadModel\Model\ContentType;
use Tulia\Cms\Content\Type\Domain\ReadModel\Service\ContentTypeDecoratorInterface;

/**
 * @author Adam Banaszkiewicz
 */
class SeoDecorator implements ContentTypeDecoratorInterface
{
    public function decorate(ContentType $contentType): void
    {
        $group = $contentType->newFieldsGroup('seo', 'main', 'SEO');
        $group->newField('seo_title', 'text', 'title', ['mutlilingual' => true, 'configuration' => ['help' => 'Fill to overwrite default title'], 'translation_domain' => 'seo']);
        $group->newField('seo_description', 'text', 'metaDescription', ['mutlilingual' => true, 'translation_domain' => 'seo']);
        $group->newField('seo_keywords', 'text', 'metaKeywords', ['mutlilingual' => true, 'translation_domain' => 'seo']);
        $group->newField('seo_og_image', 'filepicker', 'metaOgImage', ['translation_domain' => 'seo']);
        // todo Finish Robots metatag
        /*$group->newField('seo_robots', 'select', 'metaRobots', [
            'flags' => ['nonscalar_value' => true],
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
        ]);*/
    }
}
