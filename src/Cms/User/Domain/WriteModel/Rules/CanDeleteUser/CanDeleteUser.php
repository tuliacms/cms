<?php

declare(strict_types=1);

namespace Tulia\Cms\User\Domain\WriteModel\Rules\CanDeleteUser;

use Tulia\Cms\User\Application\Service\AuthenticatedUserProviderInterface;

/**
 * @author Adam Banaszkiewicz
 */
final class CanDeleteUser implements CanDeleteUserInterface
{
    public function __construct(
        private AuthenticatedUserProviderInterface $authenticatedUserProvider
    ) {
    }

    public function decide(string $userId): CanDeleteUserReasonEnum
    {
        if ($userId === $this->authenticatedUserProvider->getUser()->getId()) {
            return CanDeleteUserReasonEnum::CannotDeleteYourself;
        }

        return CanDeleteUserReasonEnum::OK;
    }
}
