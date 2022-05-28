<?php

declare(strict_types=1);

namespace Tulia\Cms\Taxonomy\Domain\ContentBuilder;

use Tulia\Cms\Content\Type\Domain\ReadModel\Model\ContentType;
use Tulia\Cms\Content\Type\Domain\ReadModel\Model\Field;
use Tulia\Cms\Content\Type\Domain\ReadModel\Service\ContentTypeDecoratorInterface;

/**
 * @author Adam Banaszkiewicz
 */
class NameSlugDecorator implements ContentTypeDecoratorInterface
{
    public function decorate(ContentType $contentType): void
    {
        if ($contentType->isType(['taxonomy']) === false) {
            return;
        }

        $contentType->addField(new Field([
            'code' => 'title',
            'type' => 'text',
            'name' => 'title',
            'flags' => [ 'multilingual' ],
            'constraints' => [
                'required' => [],
            ],
        ]));

        if ($contentType->isRoutable()) {
            $contentType->addField(
                new Field([
                    'code' => 'slug',
                    'type' => 'text',
                    'name' => 'slug',
                    'flags' => [ 'multilingual' ],
                    // @todo Create constraint for globally uniqueness of the slug
                    /*'constraints' => [
                        ['name' => 'unique', 'flags' => 'globally'],
                    ],*/
                ])
            );
        }
    }
}
