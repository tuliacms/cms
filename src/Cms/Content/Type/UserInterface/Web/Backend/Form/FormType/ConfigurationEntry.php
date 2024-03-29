<?php

declare(strict_types=1);

namespace Tulia\Cms\Content\Type\UserInterface\Web\Backend\Form\FormType;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Callback;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Context\ExecutionContextInterface;
use Tulia\Cms\Content\Type\Domain\ReadModel\Service\FieldTypeMappingRegistry;

/**
 * @author Adam Banaszkiewicz
 */
class ConfigurationEntry extends AbstractType
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
        $builder
            ->add('code', TextType::class, [
                'constraints' => [
                    new NotBlank(),
                    new Callback([$this, 'validateConfigurationExists'], null, ['field_type' => $options['field_type']]),
                ],
            ])
        ;

        $builder->addEventListener(FormEvents::PRE_SUBMIT, [$this, 'addValueField']);
    }

    public function addValueField(FormEvent $event): void
    {
        $configuration = $event->getData();
        $form = $event->getForm();

        $form
            ->add('value', TextType::class, [
                'constraints' => [
                    new Callback(
                        [$this, 'validateConfiguraionRequirementExists'],
                        null,
                        [
                            'field_type' => $event->getForm()->getConfig()->getOption('field_type'),
                            'configuration_code' => $configuration['code'],
                        ],
                    ),
                ],
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setRequired('field_type');
    }

    public function validateConfigurationExists(string $configurationCode, ExecutionContextInterface $context, array $payload): void
    {
        // Validation of node type existence is done in parent
        if ($this->fieldTypeMappingRegistry->hasType($payload['field_type']) === false) {
            return;
        }

        $type = $this->fieldTypeMappingRegistry->get($payload['field_type']);

        if (isset($type['configuration'][$configurationCode]) === false) {
            $context->buildViolation('Configuration entry named "%name%" not exists in "%type%" field type.')
                ->setParameter('%name%', $configurationCode)
                ->setParameter('%type%', $payload['field_type'])
                ->addViolation();
        }
    }

    public function validateConfiguraionRequirementExists(?string $value, ExecutionContextInterface $context, array $payload): void
    {
        $fieldType = $payload['field_type'];
        $configurationCode = $payload['configuration_code'];

        // Validation of node type existence is done in parent
        if ($this->fieldTypeMappingRegistry->hasType($fieldType) === false) {
            return;
        }

        $type = $this->fieldTypeMappingRegistry->get($fieldType);

        // COnfiguration existence is done in self::validateConfigurationExists()
        if (isset($type['configuration'][$configurationCode]) === false) {
            return;
        }

        if ($type['configuration'][$configurationCode]['required'] && $this->isEmpty($value)) {
            $context->buildViolation('Configuration entry "%name%" for "%type%" field type is required, please fill it.')
                ->setParameter('%name%', $configurationCode)
                ->setParameter('%type%', $fieldType)
                ->addViolation();
        }
    }

    private function isEmpty(?string $value): bool
    {
        return $value === null || $value === '';
    }
}
