<?php

declare(strict_types=1);

namespace Tulia\Cms\ContactForm\Domain\WriteModel;

use Tulia\Cms\ContactForm\Domain\WriteModel\Model\Form;

/**
 * @author Adam Banaszkiewicz
 */
interface FormRepositoryInterface
{
    public function createNew(string $locale): Form;
    public function find(string $id, string $locale): Form;
    public function insert(Form $form): void;
    public function update(Form $form): void;
    public function delete(Form $form): void;
}
