<?php

declare(strict_types=1);

namespace Tulia\Cms\Content\Type\Domain\ReadModel\Service;

use Tulia\Cms\Content\Type\Domain\ReadModel\ContentTypeBuilder\ContentTypeCollector;

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

    public function decorate(ContentTypeCollector $collector): void
    {
        foreach ($this->decorators as $decorator) {
            $decorator->decorate($collector);
        }
    }
}
