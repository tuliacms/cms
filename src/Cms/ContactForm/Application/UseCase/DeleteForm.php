<?php

declare(strict_types=1);

namespace Tulia\Cms\ContactForm\Application\UseCase;

use Tulia\Cms\ContactForm\Domain\WriteModel\ContactFormRepositoryInterface;
use Tulia\Cms\Shared\Application\UseCase\AbstractTransactionalUseCase;
use Tulia\Cms\Shared\Application\UseCase\RequestInterface;
use Tulia\Cms\Shared\Application\UseCase\ResultInterface;

/**
 * @author Adam Banaszkiewicz
 */
final class DeleteForm extends AbstractTransactionalUseCase
{
    public function __construct(
        private readonly ContactFormRepositoryInterface $repository
    ) {
    }

    /**
     * @param RequestInterface&DeleteFormRequest $request
     */
    protected function execute(RequestInterface $request): ?ResultInterface
    {
        $form = $this->repository->get($request->id);
        $this->repository->delete($form);

        return null;
    }
}
