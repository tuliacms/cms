<?php

declare(strict_types=1);

namespace Tulia\Cms\ContactForm\Application\UseCase;

use Tulia\Cms\Shared\Application\UseCase\AbstractTransactionalUseCase;
use Tulia\Cms\Shared\Application\UseCase\RequestInterface;
use Tulia\Cms\Shared\Application\UseCase\ResultInterface;

/**
 * @author Adam Banaszkiewicz
 */
final class CreateForm extends AbstractTransactionalUseCase
{
    public function __construct()
    {
    }

    protected function execute(RequestInterface $request): ?ResultInterface
    {
        // TODO: Implement execute() method.
    }
}
