<?php

declare(strict_types=1);

namespace Tulia\Cms\ContactForm\Domain\WriteModel\Model;

/**
 * @author Adam Banaszkiewicz
 * @final
 */
class Field
{
    private string $id;

    public function __construct(
        private FormTranslation $translation,
        public string $name,
        public string $type,
        public string $locale,
        public array $options,
    ) {
    }

    /**
     * @return Field[]
     */
    public static function createCollection(FormTranslation $translation, array $source, string $locale): array
    {
        $fields = [];

        foreach ($source as $field) {
            $fields[] = new self(
                $translation,
                $field['name'],
                $field['type_alias'],
                $locale,
                $field['options']
            );
        }

        return $fields;
    }
}
