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
use Tulia\Cms\Content\Type\Domain\ReadModel\Service\ConstraintsBuilder;
use Tulia\Cms\Content\Type\Domain\ReadModel\Service\FieldTypeMappingRegistry;
use Tulia\Cms\Platform\Infrastructure\Framework\Form\FormType\CancelType;
use Tulia\Cms\Platform\Infrastructure\Framework\Form\FormType\SubmitType;
use Tulia\Component\Routing\Website\WebsiteInterface;

/**
 * @author Adam Banaszkiewicz
 */
class SymfonyFormBuilderCreator
{
    private FormFactoryInterface $formFactory;
    private FieldTypeMappingRegistry $mappingRegistry;
    private ConstraintsBuilder $constraintsBuilder;
    private LoggerInterface $logger;
    private SymfonyFieldBuilder $symfonyFieldBuilder;

    public function __construct(
        FormFactoryInterface $formFactory,
        FieldTypeMappingRegistry $mappingRegistry,
        ConstraintsBuilder $constraintsBuilder,
        LoggerInterface $contentBuilderLogger,
        SymfonyFieldBuilder $symfonyFieldBuilder
    ) {
        $this->formFactory = $formFactory;
        $this->mappingRegistry = $mappingRegistry;
        $this->constraintsBuilder = $constraintsBuilder;
        $this->logger = $contentBuilderLogger;
        $this->symfonyFieldBuilder = $symfonyFieldBuilder;
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
        $fields = $contentType->getFields();

        $builder = $this->createFormBuilder(
            $contentType->getCode(),
            $this->flattenAttributesValues($fields, $attributes),
            $expectCqrsToken
        );

        $this->buildFieldsWithBuilder($builder, $contentType, $fields, $website);

        return $builder;
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

        $builder
            ->add('cancel', CancelType::class, [
                // @todo Configure back button URL
                'route' => 'backend.widget',
            ])
            ->add('save', SubmitType::class);
    }

    /**
     * @param Field[] $fields
     * @param Attribute[]|array<string, Attribute[]> $attributes
     */
    private function flattenAttributesValues(array $fields, array $attributes): array
    {
        $result = [];

        foreach ($fields as $code => $field) {
            if (isset($attributes[$code]) === false) {
                continue;
            }

            $attribute = $attributes[$code];

            if ($attribute instanceof Attribute) {
                $typeBuilder = $this->mappingRegistry->getTypeBuilder($field->getType());

                if ($typeBuilder) {
                    $result[$code] = $typeBuilder->buildValueFromAttribute($field, $attribute);
                } else {
                    $result[$code] = $attribute->getValue();
                }
            }

            if (is_array($attribute)) {
                foreach ($attribute as $sk => $sv) {
                    $result[$code][$sk] = $this->flattenAttributesValues($field->getChildren(), $sv);
                }
            }
        }

        return $result;
    }
}
