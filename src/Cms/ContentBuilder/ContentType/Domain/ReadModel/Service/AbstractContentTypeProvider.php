<?php

declare(strict_types=1);

namespace Tulia\Cms\ContentBuilder\ContentType\Domain\ReadModel\Service;

use Tulia\Cms\ContentBuilder\ContentType\Domain\ReadModel\Model\ContentType;

/**
 * @author Adam Banaszkiewicz
 */
abstract class AbstractContentTypeProvider implements ContentTypeProviderInterface
{
    protected ArrayToReadModelTransformer $arrayToReadModelTransformer;

    public function setArrayToReadModelTransformer(ArrayToReadModelTransformer $arrayToReadModelTransformer): void
    {
        $this->arrayToReadModelTransformer = $arrayToReadModelTransformer;
    }

    public function buildFromArray(array $type): ContentType
    {
        return $this->arrayToReadModelTransformer->transform($type);
    }
}
