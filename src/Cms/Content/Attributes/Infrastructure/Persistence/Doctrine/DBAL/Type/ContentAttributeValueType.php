<?php

declare(strict_types=1);

namespace Tulia\Cms\Content\Attributes\Infrastructure\Persistence\Doctrine\DBAL\Type;

use Doctrine\DBAL\Platforms\AbstractPlatform;
use Doctrine\DBAL\Types\Type;
use Tulia\Cms\Content\Attributes\Domain\WriteModel\Model\AttributeValue;

/**
 * @author Adam Banaszkiewicz
 */
final class ContentAttributeValueType extends Type
{
    public function getSQLDeclaration(array $fieldDeclaration, AbstractPlatform $platform): string
    {
        return 'LONGTEXT';
    }

    public function convertToPHPValue(mixed $value, AbstractPlatform $platform): mixed
    {
        if ($value && strncmp($value, '["', 2) === 0) {
            return json_decode($value, true, 2, JSON_THROW_ON_ERROR);
        }

        return [$value];
    }

    public function convertToDatabaseValue(mixed $value, AbstractPlatform $platform): mixed
    {
        if (is_array($value)) {
            if (count($value) > 1) {
                return json_encode($value);
            }

            return reset($value);
        }

        return $value;
    }

    public function getName(): string
    {
        return 'cms_content_attribute_value';
    }
}
