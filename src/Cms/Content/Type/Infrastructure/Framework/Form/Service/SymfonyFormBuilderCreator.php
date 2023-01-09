<?php

declare(strict_types=1);

namespace Tulia\Cms\Content\Type\Infrastructure\Framework\Form\Service;

use Psr\Log\LoggerInterface;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormFactoryInterface;
use Tulia\Cms\Content\Attributes\Domain\WriteModel\Model\Attribute;
use Tulia\Cms\Content\Type\Domain\ReadModel\Exception\ConstraintNotExistsException;
use Tulia\Cms\Content\Type\Domain\ReadModel\Exception\FieldTypeNotExistsException;
use Tulia\Cms\Content\Type\Domain\ReadModel\Model\ContentType;
use Tulia\Cms\Content\Type\Domain\ReadModel\Model\Field;
use Tulia\Cms\Platform\Infrastructure\Framework\Routing\Website\WebsiteInterface;

/**
 * @author Adam Banaszkiewicz
 */
class SymfonyFormBuilderCreator
{
    public function __construct(
        private readonly FormFactoryInterface $formFactory,
        private readonly LoggerInterface $logger,
        private readonly SymfonyFieldBuilder $symfonyFieldBuilder,
        private readonly AttributesDataFlattener $attributesDataFlattener,
    ) {
    }

    /**
     * @param Attribute[] $attributes
     */
    public function createBuilder(
        WebsiteInterface $website,
        ContentType $contentType,
        array $attributes,
        bool $expectCqrsToken = true
    ): FormBuilderInterface {
        $builder = $this->createFormBuilder(
            $contentType->getCode(),
            $this->attributesDataFlattener->flattenAttributes($attributes, $contentType),
            $expectCqrsToken
        );

        $this->buildFieldsWithBuilder($builder, $contentType, $contentType->getFields(), $website);

        return $builder;
    }

    public function buildUsingBuilder(
        FormBuilderInterface $builder,
        WebsiteInterface $website,
        ContentType $contentType,
        array $attributes,
    ): void {
        $this->buildFieldsWithBuilder($builder, $contentType, $contentType->getFields(), $website);
    }

    private function createFormBuilder(string $type, array $data, bool $expectCqrsToken = true): FormBuilderInterface
    {
        return $this->formFactory->createNamedBuilder(
            sprintf('content_builder_form_%s', $type),
            'Symfony\Component\Form\Extension\Core\Type\FormType',
            $data,
            [
                'csrf_protection' => $expectCqrsToken,
                'attr' => [
                    'class' => 'tulia-dynamic-form'
                ],
            ]
        );
    }

    /**
     * @param Field[] $fields
     * @param Attribute[] $attributes
     */
    private function buildFieldsWithBuilder(
        FormBuilderInterface $builder,
        ContentType $contentType,
        array $fields,
        WebsiteInterface $website,
    ): void {
        /** @var Field $field */
        foreach ($fields as $field) {
            try {
                $this->symfonyFieldBuilder->buildFieldAndAddToBuilder(
                    $builder,
                    $contentType,
                    $field,
                    $website,
                );
            } catch (ConstraintNotExistsException $e) {
                $this->logger->warning(
                    sprintf(
                        'Cms\ContentBuilder: Constraint "%s" not exists. Field "%s" wasn\'t created in form.',
                        $e->getName(),
                        $field->getCode()
                    )
                );
            } catch (FieldTypeNotExistsException $e) {
                $this->logger->warning(
                    sprintf(
                        'Cms\ContentBuilder: Mapping for field type "%s" not exists. Field "%s" wasn\'t created in form.',
                        $field->getType(),
                        $field->getCode()
                    )
                );
            }
        }
    }
}
