<?php

declare(strict_types=1);

namespace Tulia\Cms\ContactForm\Infrastructure\FormBuilder;

use Symfony\Component\Form\FormInterface;
use Tulia\Cms\ContactForm\Domain\ReadModel\Model\Form;

/**
 * @author Adam Banaszkiewicz
 */
interface ContactFormBuilderInterface
{
    public function build(Form $form, array $data = [], array $options = []): FormInterface;
}
