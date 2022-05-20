<?php

declare(strict_types=1);

namespace Tulia\Cms\Node\UserInterface\Web\Shared\Form\FormType;

use Symfony\Component\Validator\Constraints\Callback;
use Symfony\Component\Validator\Context\ExecutionContextInterface;
use Tulia\Cms\ContentBuilder\ContentType\Domain\ReadModel\FieldTypeBuilder\AbstractFieldTypeBuilder;
use Tulia\Cms\ContentBuilder\ContentType\Domain\ReadModel\Model\ContentType;
use Tulia\Cms\ContentBuilder\ContentType\Domain\ReadModel\Model\Field;

/**
 * @author Adam Banaszkiewicz
 */
class NodeTypeaheadTypeBuilder extends AbstractFieldTypeBuilder
{
    public function buildOptions(Field $field, array $options, ContentType $contentType): array
    {
        $options['search_route_params'] = [
            'node_type' => $contentType->getCode(),
        ];
        $options['constraints'] += [
            new Callback(function ($value, ExecutionContextInterface $context) {
                if (empty($value) === false && $value === $context->getRoot()->get('id')->getData()) {
                    $context->buildViolation('cannotAssignSelfNodeParent')
                        ->setTranslationDomain('node')
                        ->atPath('parent_id')
                        ->addViolation();
                }
            }),
        ];

        return $options;
    }
}
