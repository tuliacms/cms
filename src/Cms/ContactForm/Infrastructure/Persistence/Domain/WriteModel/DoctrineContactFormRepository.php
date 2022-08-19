<?php

declare(strict_types=1);

namespace Tulia\Cms\ContactForm\Infrastructure\Persistence\Domain\WriteModel;

use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\Uid\Uuid;
use Tulia\Cms\ContactForm\Domain\Exception\FormNotFoundException;
use Tulia\Cms\ContactForm\Domain\WriteModel\ContactFormRepositoryInterface;
use Tulia\Cms\ContactForm\Domain\WriteModel\NewModel\Form;

/**
 * @author Adam Banaszkiewicz
 */
final class DoctrineContactFormRepository extends ServiceEntityRepository implements ContactFormRepositoryInterface
{
    public function __construct(
        ManagerRegistry $registry
    ) {
        parent::__construct($registry, Form::class);
    }

    public function generateNextId(): string
    {
        return (string) Uuid::v4();
    }

    public function get(string $id): Form
    {
        $form = $this->find($id);

        if (!$form) {
            throw new FormNotFoundException(sprintf('Form %s not found.', $id));
        }

        return $form;
    }

    public function save(Form $form): void
    {
        $this->_em->persist($form);
        $this->_em->flush();
    }

    public function delete(Form $form): void
    {
        $this->_em->remove($form);
        $this->_em->flush();
    }
}
