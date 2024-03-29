<?php

declare(strict_types=1);

namespace Tulia\Cms\User\Infrastructure\Framework\Form\FormType\UserTypeahead;

use Tulia\Cms\Content\Type\Domain\ReadModel\FieldTypeBuilder\AbstractFieldTypeBuilder;
use Tulia\Cms\Content\Type\Domain\ReadModel\Model\ContentType;
use Tulia\Cms\Content\Type\Domain\ReadModel\Model\Field;
use Tulia\Cms\User\Application\Service\AuthenticatedUserProviderInterface;
use Tulia\Cms\User\Domain\ReadModel\Model\User;

/**
 * @author Adam Banaszkiewicz
 */
class UserTypeaheadTypeBuilder extends AbstractFieldTypeBuilder
{
    protected AuthenticatedUserProviderInterface $authenticatedUserProvider;

    public function __construct(AuthenticatedUserProviderInterface $authenticatedUserProvider)
    {
        $this->authenticatedUserProvider = $authenticatedUserProvider;
    }

    public function buildOptions(Field $field, array $options, ContentType $contentType): array
    {
        /** @var User $author */
        $author = $this->authenticatedUserProvider->getUser();

        return [
            'empty_data' => $author->getId(),
        ];
    }
}
