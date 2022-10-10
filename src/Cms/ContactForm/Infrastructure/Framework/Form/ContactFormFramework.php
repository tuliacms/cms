<?php

declare(strict_types=1);

namespace Tulia\Cms\ContactForm\Infrastructure\Framework\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Tulia\Cms\ContactForm\Domain\FieldType\FieldsTypeRegistryInterface;
use Tulia\Cms\ContactForm\Domain\ReadModel\Model\Field;
use Tulia\Cms\Content\Type\Domain\ReadModel\Service\ConstraintsResolverInterface;

/**
 * @author Adam Banaszkiewicz
 */
class ContactFormFramework extends AbstractType
{
    public function __construct(
        private readonly FieldsTypeRegistryInterface $typesRegistry,
        private readonly ConstraintsResolverInterface $constraintsResolver
    ) {
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        /** @var Field $field */
        foreach ($options['fields'] as $field) {
            $type = $this->typesRegistry->get($field->getType());
            $options = $this->buildOptions($field->getOptions());

            $builder->add(
                $field->getName(),
                $type->getFormType(),
                $type->buildOptions($options)
            );
        }
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setRequired('fields');
        $resolver->setAllowedTypes('fields', 'array');
    }

    protected function buildOptions(array $options): array
    {
        /**
         * If user leave empty label in builder, this means he wants label to
         * not show in form. So any value casted to false must removes the label.
         */
        if (!$options['label']) {
            $options['label'] = false;
        }

        return $this->buildConstraints($options);
    }

    protected function buildConstraints(array $options): array
    {
        if (isset($options['constraints'])) {
            $constraints = [];

            $options['constraints'] = explode(',', $options['constraints']);
            $options['constraints'] = array_map('trim', $options['constraints']);
            $options = $this->constraintsResolver->resolve($options);

            foreach ($options['constraints'] as $constraint) {
                $constraints[] = new $constraint['classname'](...$constraint['modificators']);
            }

            $options['constraints'] = $constraints;
        }

        return $options;
    }
}
