<?php

declare(strict_types=1);

namespace Tulia\Cms\Content\Type\Infrastructure\Framework\Form;

use Symfony\Component\Form\AbstractTypeExtension;
use Symfony\Component\Form\Extension\Core\Type\FormType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Tulia\Cms\Content\Type\Domain\ReadModel\Model\Field;
use Tulia\Cms\Content\Type\Domain\ReadModel\FieldTypeHandler\FieldTypeHandlerInterface;

/**
 * @author Adam Banaszkiewicz
 */
class ContentBuilderFormExtension extends AbstractTypeExtension
{
    public static function getExtendedTypes(): iterable
    {
        return [FormType::class];
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setRequired('content_builder_field');
        $resolver->setAllowedTypes('content_builder_field', ['null', Field::class]);
        $resolver->setDefault('content_builder_field', null);

        $resolver->setRequired('content_builder_field_handler');
        $resolver->setAllowedTypes('content_builder_field_handler', ['null', FieldTypeHandlerInterface::class]);
        $resolver->setDefault('content_builder_field_handler', null);

        $resolver->setRequired('locale');
        $resolver->setAllowedTypes('locale', ['null', 'string']);
        $resolver->setDefault('locale', null);

        $resolver->setRequired('website_id');
        $resolver->setAllowedTypes('website_id', ['null', 'string']);
        $resolver->setDefault('website_id', null);
    }
}
