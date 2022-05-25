<?php

declare(strict_types=1);

namespace Tulia\Cms\Content\Type\Domain\ReadModel\Service;

use Tulia\Cms\Content\Type\Domain\ReadModel\Model\ContentType;

/**
 * @author Adam Banaszkiewicz
 */
class ContentTypeDecorator
{
    /**
     * @var ContentTypeDecoratorInterface[]
     */
    protected array $decorators = [];

    public function addDecorator(ContentTypeDecoratorInterface $decorator): void
    {
        $this->decorators[] = $decorator;
    }

    public function decorate(ContentType $contentType): void
    {
        foreach ($this->decorators as $decorator) {
            $decorator->decorate($contentType);
        }
    }
}
