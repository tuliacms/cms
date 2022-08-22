<?php

declare(strict_types=1);

namespace Tulia\Cms\ContactForm\Application\UseCase;

use Tulia\Cms\ContactForm\Domain\FieldsParser\FieldsParserInterface;
use Tulia\Cms\ContactForm\Domain\WriteModel\ContactFormRepositoryInterface;
use Tulia\Cms\ContactForm\Domain\WriteModel\Model\Form;
use Tulia\Cms\Shared\Application\UseCase\AbstractTransactionalUseCase;
use Tulia\Cms\Shared\Application\UseCase\IdResult;
use Tulia\Cms\Shared\Application\UseCase\RequestInterface;
use Tulia\Cms\Shared\Application\UseCase\ResultInterface;
use Tulia\Cms\Shared\Infrastructure\Bus\Event\EventBusInterface;

/**
 * @author Adam Banaszkiewicz
 */
final class CreateForm extends AbstractTransactionalUseCase
{
    public function __construct(
        private readonly ContactFormRepositoryInterface $repository,
        private readonly EventBusInterface $eventBus,
        private readonly FieldsParserInterface $fieldsParser
    ) {
    }

    /**
     * @param RequestInterface&CreateFormRequest $request
     */
    protected function execute(RequestInterface $request): ?ResultInterface
    {
        $form = Form::create(
            $this->repository->generateNextId(),
            $request->name,
            $request->subject,
            $request->senderEmail,
            $request->senderName,
            $request->receivers,
            $request->replyTo,
            $this->fieldsParser,
            $this->collectFields($request->fields),
            $request->fieldsTemplate,
            $request->messageTemplate,
            $request->locale,
            $request->defaultLocale,
            $request->localeCodes,
        );

        $this->repository->save($form);
        $this->eventBus->dispatchCollection($form->collectDomainEvents());

        return new IdResult($form->getId());
    }

    private function collectFields(array $source): array
    {
        $fields = [];

        foreach ($source as $field) {
            $name = $field['name'];
            $type = $field['alias'];

            unset($field['name'], $field['alias']);

            $fields[] = [
                'name' => $name,
                'type' => $type,
                'options' => $field
            ];
        }

        return $fields;
    }
}
