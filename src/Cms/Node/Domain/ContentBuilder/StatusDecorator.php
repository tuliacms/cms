<?php

declare(strict_types=1);

namespace Tulia\Cms\Node\Domain\ContentBuilder;

use Tulia\Cms\Content\Type\Domain\ReadModel\Model\ContentType;
use Tulia\Cms\Content\Type\Domain\ReadModel\Model\Field;
use Tulia\Cms\Content\Type\Domain\ReadModel\Service\ContentTypeDecoratorInterface;

/**
 * @author Adam Banaszkiewicz
 */
class StatusDecorator implements ContentTypeDecoratorInterface
{
    public function decorate(ContentType $contentType): void
    {
        if ($contentType->isType('node') === false) {
            return;
        }

        $contentType->addField(new Field([
            'code' => 'status',
            'type' => 'node_status',
            'name' => 'publicationStatus',
            'constraints' => [
                'required' => [],
            ]
        ]));
    }
}
