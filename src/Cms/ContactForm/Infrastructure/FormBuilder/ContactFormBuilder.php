<?php

declare(strict_types=1);

namespace Tulia\Cms\ContactForm\Infrastructure\FormBuilder;

use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Tulia\Cms\ContactForm\Domain\ReadModel\Model\Form;
use Tulia\Cms\ContactForm\Infrastructure\Framework\Form\ContactFormFramework;

/**
 * @author Adam Banaszkiewicz
 */
class ContactFormBuilder implements ContactFormBuilderInterface
{
    public function __construct(
        private readonly FormFactoryInterface $formFactory,
        private readonly UrlGeneratorInterface $router,
    ) {
    }

    public function build(Form $form, array $data = [], array $options = []): FormInterface
    {
        $options = array_merge([
            'fields' => $form->getFields(),
            'action' => $this->router->generate('frontend.form.submit', [ 'id' => $form->getId() ]),
        ], $options);

        return $this->formFactory->createNamed(
            'contact_form_' . $form->getId(),
            ContactFormFramework::class,
            $data,
            $options
        );
    }
}
