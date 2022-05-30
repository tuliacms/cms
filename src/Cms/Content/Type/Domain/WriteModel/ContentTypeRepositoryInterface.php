<?php

declare(strict_types=1);

namespace Tulia\Cms\Content\Type\Domain\WriteModel;

use Tulia\Cms\Content\Type\Domain\WriteModel\Model\ContentType;

/**
 * @author Adam Banaszkiewicz
 */
interface ContentTypeRepositoryInterface
{
    public function find(string $code): ?ContentType;
    public function insert(ContentType $contentType): void;
    public function update(ContentType $contentType): void;
    public function delete(ContentType $contentType): void;
}
