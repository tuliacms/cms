<?php

declare(strict_types=1);

namespace Tulia\Cms\Content\Type\UserInterface\Web\Backend\Form\FormType;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Callback;
use Symfony\Component\Validator\Constraints\Choice;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Context\ExecutionContextInterface;
use Tulia\Cms\Content\Type\Domain\ReadModel\Service\FieldTypeMappingRegistry;
use Tulia\Cms\Content\Type\Infrastructure\Framework\Form\Validator\CodenameValidator;

/**
 * @author Adam Banaszkiewicz
 */
class Field extends AbstractType
{
    private FieldTypeMappingRegistry $fieldTypeMappingRegistry;

    public function __construct(FieldTypeMappingRegistry $fieldTypeMappingRegistry)
    {
        $this->fieldTypeMappingRegistry = $fieldTypeMappingRegistry;
    }

    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $choices = ['Yes' => true, 'No' => false];

        $builder
            ->add('code', TextType::class, [
                'constraints' => [
                    new NotBlank(),
                    new Callback([new CodenameValidator(), 'validateFieldCode']),
                ],
            ])
            ->add('type', TextType::class, [
                'constraints' => [
                    new NotBlank(),
                    new Callback([$this, 'validateFieldTypeExists']),
                ],
            ])
            ->add('multilingual', ChoiceType::class, [
                'choices' => $choices,
                'constraints' => [
                    new Choice(['choices' => $choices]),
                ],
            ])
        ;

        if ($options['max_depth_fields'] > 0) {
            $builder->add('children', CollectionType::class, [
                'entry_type' => Field::class,
                'allow_add' => true,
                'allow_delete' => true,
                'entry_options' => [
                    'max_depth_fields' => $options['max_depth_fields'] - 1,
                ],
            ]);
        }

        $builder->addEventListener(FormEvents::PRE_SUBMIT, [$this, 'addDynamicFields']);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setRequired('max_depth_fields');
    }

    public function addDynamicFields(FormEvent $event): void
    {
        $field = $event->getData();
        $form = $event->getForm();

        $form
            ->add('name', TextType::class, [
                'constraints' => [
                    new NotBlank(),
                    new Callback(
                        [$this, 'validateFieldTypeRequiredConfigurationExistence'],
                        null,
                        [
                            'configurations' => $field['configuration'],
                            'field_type' => $field['type'],
                        ],
                    ),
                ],
            ])
            ->add('constraints', CollectionType::class, [
                'entry_type' => Constraint::class,
                'allow_add' => true,
                'allow_delete' => true,
                'entry_options' => [
                    'field_type' => $field['type'],
                ],
            ])
            ->add('configuration', CollectionType::class, [
                'entry_type' => ConfigurationEntry::class,
                'allow_add' => true,
                'allow_delete' => true,
                'entry_options' => [
                    'field_type' => $field['type'],
                ],
            ])
        ;
    }

    public function validateFieldTypeExists(string $type, ExecutionContextInterface $context): void
    {
        if ($this->fieldTypeMappingRegistry->hasType($type) === false) {
            $context->buildViolation('fieldTypeNotExists')
                ->setTranslationDomain('content_builder')
                ->setParameter('%type%', $type)
                ->addViolation();
        }
    }

    public function validateFieldTypeRequiredConfigurationExistence(?string $label, ExecutionContextInterface $context, array $payload): void
    {
        // Validation of the field type is done in self::validateFieldTypeExists()
        if ($this->fieldTypeMappingRegistry->hasType($payload['field_type']) === false) {
            return;
        }

        $type = $this->fieldTypeMappingRegistry->get($payload['field_type']);

        foreach ($type['configuration'] as $configurationCode => $requiredConfiguration) {
            if ($requiredConfiguration['required'] === false) {
                continue;
            }

            $found = false;

            foreach ($payload['configurations'] as $filledConfiguration) {
                if ($filledConfiguration['code'] === $configurationCode) {
                    $found = true;
                }
            }

            if ($found === false) {
                $context->buildViolation('Configuration named "%name%" for field type "%type%" is required, please fill it.')
                    ->setTranslationDomain('content_builder')
                    ->setParameter('%name%', $configurationCode)
                    ->setParameter('%type%', $payload['field_type'])
                    ->addViolation();
            }
        }
    }
}
