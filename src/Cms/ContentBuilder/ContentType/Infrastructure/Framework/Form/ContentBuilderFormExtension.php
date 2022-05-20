<?php

declare(strict_types=1);

namespace Tulia\Cms\ContentBuilder\ContentType\Infrastructure\Framework\Form;

use Symfony\Component\Form\AbstractTypeExtension;
use Symfony\Component\Form\Extension\Core\Type\FormType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Tulia\Cms\ContentBuilder\ContentType\Domain\AbstractModel\AbstractField;
use Tulia\Cms\ContentBuilder\ContentType\Domain\ReadModel\FieldTypeHandler\FieldTypeHandlerInterface;

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
        $resolver->setAllowedTypes('content_builder_field', ['null', AbstractField::class]);
        $resolver->setDefault('content_builder_field', null);

        $resolver->setRequired('content_builder_field_handler');
        $resolver->setAllowedTypes('content_builder_field_handler', ['null', FieldTypeHandlerInterface::class]);
        $resolver->setDefault('content_builder_field_handler', null);
    }
}
