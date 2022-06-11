<?php

declare(strict_types=1);

namespace Tulia\Cms\Content\Type\Domain\WriteModel\Rules;

/**
 * @author Adam Banaszkiewicz
 */
enum CanCreateContentTypeReason: string
{
    case TypeOfContentTypeNotExists = 'Type of content type not exists';
    case CodeContainsNotAllowedCharacters = 'Code contains not allowed characters';
    case ContentTypeWithThisCodeAlreadyExists = 'Content type with this code already exists';
    case OK = 'OK';
}
