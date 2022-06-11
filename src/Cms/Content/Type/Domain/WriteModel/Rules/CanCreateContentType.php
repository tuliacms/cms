<?php

declare(strict_types=1);

namespace Tulia\Cms\Content\Type\Domain\WriteModel\Rules;

use Tulia\Cms\Content\Type\Domain\ReadModel\Service\ContentTypeRegistryInterface;
use Tulia\Cms\Content\Type\Domain\WriteModel\Service\ContentTypeExistanceDetectorInterface;

/**
 * @author Adam Banaszkiewicz
 */
final class CanCreateContentType implements CanCreateContentTypeInterface
{
    public function __construct(
        private ContentTypeRegistryInterface $contentTypeRegistry,
        private ContentTypeExistanceDetectorInterface $detector
    ) {
    }

    public function decide(string $type, string $code): CanCreateContentTypeReason
    {
        if ($this->typeOfContentTypeNotExists($type)) {
            return CanCreateContentTypeReason::TypeOfContentTypeNotExists;
        }

        if ($this->codeContainsNotAllowedCharacters($code)) {
            return CanCreateContentTypeReason::CodeContainsNotAllowedCharacters;
        }

        if ($this->contentTypeWithCodeAlreadyExists($code)) {
            return CanCreateContentTypeReason::ContentTypeWithThisCodeAlreadyExists;
        }

        return CanCreateContentTypeReason::OK;
    }

    public function typeOfContentTypeNotExists(string $type): bool
    {
        return ! $this->contentTypeRegistry->has($type);
    }

    public function codeContainsNotAllowedCharacters(string $code): bool
    {
        return ! (bool) preg_match('#^[a-z0-9_]+$#', $code);
    }

    public function contentTypeWithCodeAlreadyExists(string $code): bool
    {
        return $this->detector->exists($code);
    }
}
