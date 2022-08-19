<?php

declare(strict_types=1);

namespace Tulia\Cms\ContactForm\Domain\WriteModel;

use Tulia\Cms\ContactForm\Domain\WriteModel\NewModel\Form;

/**
 * @author Adam Banaszkiewicz
 */
interface ContactFormRepositoryInterface
{
    public function generateNextId(): string;
    public function get(string $id): Form;
    public function save(Form $form): void;
    public function delete(Form $form): void;
}
